<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Cache\RecordCache;
use Atrium\Admin\Support\PublicUrl;
use Atrium\Theme\Tests\Fixtures\Brand;
use Atrium\Theme\Tests\Fixtures\Product;
use Atrium\Theme\Tests\Fixtures\ProductSection;
use Illuminate\Support\Facades\DB;

/**
 * The theme's pages of a cached module: a list costs the same queries for
 * any number of records, a warm product page almost none, and nothing is
 * stale after a save.
 */
final class CachedPagesTest extends TestCase
{
    private ProductSection $laptops;

    protected function setUp(): void
    {
        parent::setUp();

        $computers = ProductSection::query()->create(['name' => 'Computers', 'code' => 'computers']);
        $this->laptops = ProductSection::query()->create(['name' => 'Laptops', 'code' => 'laptops', 'parent_id' => $computers->id]);
    }

    public function test_a_list_page_costs_the_same_queries_for_any_number_of_records(): void
    {
        $cache = $this->app->make(RecordCache::class);
        $this->products(3);
        $this->get('/catalog')->assertOk(); // the schema is read once per process
        $cache->flushAll();
        PublicUrl::forgetPaths();
        $few = $this->queries(fn () => $this->get('/catalog')->assertOk());

        $this->products(17, 'More');
        $cache->flushAll();
        PublicUrl::forgetPaths();
        $many = $this->queries(fn () => $this->get('/catalog')->assertOk()->assertSee('More 17'));

        $this->assertSame($few, $many);
    }

    public function test_a_warm_product_page_runs_at_most_two_queries(): void
    {
        $this->products(1);
        $url = '/catalog/computers/laptops/product-1';
        $this->get($url)->assertOk()->assertSee('Product 1');

        $queries = $this->queries(fn () => $this->get($url)->assertOk()->assertSee('Product 1'));

        $this->assertLessThanOrEqual(2, $queries);
    }

    public function test_a_warm_list_page_runs_at_most_two_queries(): void
    {
        $this->products(5);
        $this->get('/catalog')->assertOk();

        $queries = $this->queries(fn () => $this->get('/catalog')->assertOk()->assertSee('Product 5'));

        $this->assertLessThanOrEqual(2, $queries);
    }

    public function test_a_save_shows_on_the_list_and_the_page_at_once(): void
    {
        [$product] = $this->products(1);
        $brand = Brand::query()->create(['name' => 'Northwind', 'xml_id' => 'northwind']);
        $product->update(['brand' => $brand->id]);
        $this->get('/catalog')->assertSee('Product 1');
        $this->get('/catalog/computers/laptops/product-1')->assertSee('Product 1');

        $product->update(['name' => 'Renamed']);

        $this->get('/catalog')->assertSee('Renamed')->assertDontSee('Product 1');
        $this->get('/catalog/computers/laptops/product-1')->assertSee('Renamed');
    }

    public function test_moving_a_section_moves_its_products_addresses(): void
    {
        $this->products(1);
        $this->get('/catalog/computers/laptops/product-1')->assertOk();

        $this->laptops->update(['code' => 'notebooks']);

        $this->get('/catalog/computers/notebooks/product-1')->assertOk()->assertSee('Product 1');
    }

    public function test_the_warm_command_fills_the_cache(): void
    {
        $this->products(3);

        $this->artisan('atrium:cache:warm', ['--records' => true])
            ->expectsOutputToContain('built 5 cards') // three products, two sections
            ->assertSuccessful();

        $cache = $this->app->make(RecordCache::class);
        $cache->reset();
        $this->get('/catalog')->assertOk();

        $this->assertSame(0, $cache->requestStats()['misses']);
    }

    /**
     * @return array<int,Product>
     */
    private function products(int $count, string $prefix = 'Product'): array
    {
        $products = [];

        for ($i = 1; $i <= $count; $i++) {
            $products[] = Product::query()->create([
                'section_id' => $this->laptops->id,
                'name' => $prefix.' '.$i,
                'code' => strtolower($prefix).'-'.$i,
                'sort' => $i,
                'preview_picture' => '/upload/iblock/1/'.$i.'.jpg',
                'detail_text' => '<p>About '.$i.'</p>',
                'photos' => json_encode(['/upload/a.jpg', 'products/b.jpg']),
            ]);
        }

        return $products;
    }

    private function queries(callable $request): int
    {
        $count = 0;
        $counting = true;
        DB::listen(function () use (&$count, &$counting): void {
            if ($counting) {
                $count++;
            }
        });
        $request();
        $counting = false;

        return $count;
    }
}
