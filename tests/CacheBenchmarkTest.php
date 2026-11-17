<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Cache\RecordCache;
use Atrium\Admin\Models\Language;
use Atrium\Admin\Support\PublicUrl;
use Atrium\Theme\Tests\Fixtures\BenchProduct;
use Atrium\Theme\Tests\Fixtures\Brand;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The cache benchmark: a catalog of 10 000 products with 40 properties
 * each, opened as a list and as a product page with the cache off, cold
 * and warm, and with the page cache on.
 *
 * Skipped unless ATRIUM_BENCHMARK=1, since it fills a big table:
 *
 *     ATRIUM_BENCHMARK=1 vendor/bin/phpunit packages/theme/tests/CacheBenchmarkTest.php
 *
 * ATRIUM_BENCHMARK_PRODUCTS changes the number of products,
 * ATRIUM_BENCHMARK_STORE the store (file, redis, database, array; file by
 * default), DB_CONNECTION the database, as for the rest of the suite.
 * ATRIUM_BENCHMARK_REPORT names a file to write the table to.
 */
final class CacheBenchmarkTest extends TestCase
{
    private const RUNS = 5;

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $config = $app['config'];
        $config->set('atrium.public_urls', [...(array) $config->get('atrium.public_urls'), BenchProduct::class => ['prefix' => 'bench']]);

        // The dashboard's page timing is written after the response is sent
        // (a visitor does not wait for it): left out of the counts.
        $config->set('atrium.dashboard.timings.enabled', false);

        $store = (string) (env('ATRIUM_BENCHMARK_STORE') ?: 'file');
        $config->set('atrium.cache.store', $store);
        $config->set('cache.stores.file.path', sys_get_temp_dir().'/atrium-cache-benchmark');
        $config->set('database.redis.client', 'phpredis');
        $config->set('database.redis.cache.host', env('REDIS_HOST', '127.0.0.1'));
        $config->set('database.redis.cache.port', (int) env('REDIS_PORT', 6379));
        $config->set('database.redis.cache.database', 1);
    }

    public function test_the_catalog_benchmark(): void
    {
        if (! env('ATRIUM_BENCHMARK')) {
            $this->markTestSkipped('Set ATRIUM_BENCHMARK=1 to run the cache benchmark.');
        }

        $count = (int) (env('ATRIUM_BENCHMARK_PRODUCTS') ?: 10000);
        $this->seedCatalog($count);
        $cache = $this->app->make(RecordCache::class);
        $cache->flushAll();

        $pages = [
            'list, page 1' => '/bench',
            'list, page '.intdiv($count, 40) => '/bench?page='.intdiv($count, 40),
            'product page' => '/bench/product-'.intdiv($count, 2),
        ];
        $rows = [];

        foreach ($pages as $label => $url) {
            $this->get($url)->assertOk(); // the schema is read once per process

            config(['atrium.cache.enabled' => false, 'atrium.cache.pages.enabled' => false]);
            $rows[$label]['off'] = $this->measure($url, fn () => null);

            config(['atrium.cache.enabled' => true]);
            $rows[$label]['cold'] = $this->measure($url, function () use ($cache): void {
                $cache->flushAll();
                PublicUrl::forgetPaths();
            });
            $rows[$label]['warm'] = $this->measure($url, fn () => null);

            config(['atrium.cache.pages.enabled' => true]);
            $this->get($url);
            $rows[$label]['page'] = $this->measure($url, fn () => null);
            config(['atrium.cache.pages.enabled' => false]);
        }

        $report = $this->report($count, $rows);
        fwrite(STDERR, PHP_EOL.$report.PHP_EOL);

        if ($file = env('ATRIUM_BENCHMARK_REPORT')) {
            file_put_contents((string) $file, $report.PHP_EOL, FILE_APPEND);
        }

        // The targets: a list in the same few queries on any page, a warm
        // product page in two at most, a kept page in none.
        $this->assertSame($rows['list, page 1']['cold']['queries'], $rows['list, page '.intdiv($count, 40)]['cold']['queries']);
        $this->assertLessThanOrEqual(2, $rows['list, page 1']['warm']['queries']);
        $this->assertLessThanOrEqual(2, $rows['product page']['warm']['queries']);
        $this->assertSame(0, $rows['product page']['page']['queries']);
    }

    /**
     * The median of a few runs: queries, wall time and this process's CPU
     * time (the wall time includes the database and the cache store).
     *
     * @return array{queries:int,ms:float,cpu:float}
     */
    private function measure(string $url, callable $before): array
    {
        $count = 0;
        $listening = false;
        DB::listen(function () use (&$count, &$listening): void {
            if ($listening) {
                $count++;
            }
        });
        $wall = [];
        $cpu = [];

        for ($run = 0; $run < self::RUNS; $run++) {
            $before();
            $count = 0;
            $listening = true;
            $usage = getrusage();
            $start = hrtime(true);
            $this->get($url)->assertOk()->assertSee('Product');
            $wall[] = (hrtime(true) - $start) / 1e6;
            $cpu[] = $this->cpu(getrusage()) - $this->cpu($usage);
            $listening = false;
        }

        sort($wall);
        sort($cpu);

        return ['queries' => $count, 'ms' => round($wall[intdiv(self::RUNS, 2)], 1), 'cpu' => round($cpu[intdiv(self::RUNS, 2)], 1)];
    }

    /**
     * @param  array<string,int>  $usage
     */
    private function cpu(array $usage): float
    {
        return $usage['ru_utime.tv_sec'] * 1000 + $usage['ru_utime.tv_usec'] / 1000 + $usage['ru_stime.tv_sec'] * 1000 + $usage['ru_stime.tv_usec'] / 1000;
    }

    /**
     * @param  array<string,array<string,array{queries:int,ms:float,cpu:float}>>  $rows
     */
    private function report(int $count, array $rows): string
    {
        $lines = [
            sprintf('%d products × %d properties, %s, %s store (median of %d; queries, wall ms / PHP CPU ms)', $count, count(BenchProduct::properties()), DB::connection()->getDriverName(), config('atrium.cache.store'), self::RUNS),
            '',
            '| Page | Cache off | Cold cache | Warm cache | Page cache |',
            '|---|---|---|---|---|',
        ];

        foreach ($rows as $label => $row) {
            $cells = array_map(fn (array $cell): string => sprintf('%d q, %.1f / %.1f ms', $cell['queries'], $cell['ms'], $cell['cpu']), $row);
            $lines[] = '| '.$label.' | '.implode(' | ', $cells).' |';
        }

        return implode(PHP_EOL, $lines);
    }

    private function seedCatalog(int $count): void
    {
        Language::query()->create(['code' => 'de', 'name' => 'Deutsch']);

        Schema::create('bench_products', function (Blueprint $table): void {
            $table->id();
            $table->boolean('active')->default(true);
            $table->dateTime('active_from')->nullable();
            $table->dateTime('active_to')->nullable();
            $table->integer('sort')->default(500);
            $table->json('name');
            $table->json('code');

            foreach (BenchProduct::properties() as $column => $kind) {
                match ($kind) {
                    'number' => $table->decimal($column, 12, 2)->nullable(),
                    'content' => $table->text($column)->nullable(),
                    'brand' => $table->unsignedBigInteger($column)->nullable(),
                    default => $table->string($column)->nullable(),
                };
            }

            $table->string('preview_picture')->nullable();
            $table->text('preview_text')->nullable();
            $table->string('detail_picture')->nullable();
            $table->json('detail_text')->nullable();
            $table->text('photos')->nullable();
            $table->string('meta_title')->nullable();
            $table->timestamps();
        });

        Schema::create('bench_related', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->index();
            $table->foreignId('related_id');
        });

        $brands = [];

        for ($i = 1; $i <= 50; $i++) {
            $brands[] = ['name' => 'Brand '.$i, 'xml_id' => 'brand-'.$i, 'created_at' => now(), 'updated_at' => now()];
        }

        Brand::query()->insert($brands);

        foreach (array_chunk(range(1, $count), 200) as $chunk) {
            $products = [];
            $related = [];
            $seo = [];

            foreach ($chunk as $i) {
                $products[] = $this->product($i);

                foreach ([1, 2, 3] as $step) {
                    $related[] = ['product_id' => $i, 'related_id' => ($i + $step * 7) % $count + 1];
                }

                $seo[] = ['seoable_type' => BenchProduct::class, 'seoable_id' => $i, 'title' => 'Buy product '.$i, 'description' => 'Product '.$i.' at the best price', 'created_at' => now(), 'updated_at' => now()];
            }

            DB::table('bench_products')->insert($products);
            DB::table('bench_related')->insert($related);
            DB::table('seo_meta')->insert($seo);
        }
    }

    /**
     * @return array<string,mixed>
     */
    private function product(int $i): array
    {
        $row = [
            'active' => true,
            'sort' => $i,
            'name' => json_encode(['en' => 'Product '.$i, 'de' => 'Produkt '.$i]),
            'code' => json_encode(['en' => 'product-'.$i, 'de' => 'produkt-'.$i]),
            'preview_picture' => '/upload/iblock/'.($i % 100).'/'.$i.'.jpg',
            'preview_text' => '<p>Short about product '.$i.'</p>',
            'detail_picture' => '/upload/iblock/'.($i % 100).'/'.$i.'-big.jpg',
            'detail_text' => json_encode(['en' => '<p>All about product '.$i.'</p>', 'de' => '<p>Alles über Produkt '.$i.'</p>']),
            'photos' => json_encode(['/upload/iblock/a/'.$i.'-1.jpg', '/upload/iblock/a/'.$i.'-2.jpg', '/upload/iblock/a/'.$i.'-3.jpg']),
            'meta_title' => 'Product '.$i,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        foreach (BenchProduct::properties() as $column => $kind) {
            $row[$column] = match ($kind) {
                'text' => 'Value '.$column.' '.$i,
                'number' => ($i * 13 % 1000) + 0.5,
                'list' => ['a', 'b', 'c'][$i % 3],
                'flag' => $i % 2 === 0 ? 'Y' : null,
                'content' => '<p>'.$column.' of product '.$i.'</p>',
                default => $i % 50 + 1,
            };
        }

        return $row;
    }
}
