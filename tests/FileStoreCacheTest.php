<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Cache\RecordCache;
use Atrium\Theme\Tests\Fixtures\Brand;
use Atrium\Theme\Tests\Fixtures\Product;
use Atrium\Theme\Tests\Fixtures\ProductSection;
use Illuminate\Support\Facades\File;

/**
 * Laravel 13 apps unpack no objects from a file, database or Redis cache
 * (cache.serializable_classes = false): cards, lists and remembered values
 * must be plain arrays, or every read is a miss.
 */
final class FileStoreCacheTest extends TestCase
{
    private string $path;

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $this->path = sys_get_temp_dir().'/atrium-file-store-test-'.getmypid();
        $app['config']->set('cache.serializable_classes', false);
        $app['config']->set('cache.stores.file.path', $this->path);
        $app['config']->set('atrium.cache.store', 'file');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->path);

        parent::tearDown();
    }

    public function test_warm_pages_come_from_a_store_that_unpacks_no_objects(): void
    {
        $computers = ProductSection::query()->create(['name' => 'Computers', 'code' => 'computers']);
        $laptops = ProductSection::query()->create(['name' => 'Laptops', 'code' => 'laptops', 'parent_id' => $computers->id]);
        $brand = Brand::query()->create(['name' => 'Northwind', 'xml_id' => 'northwind']);
        $product = Product::query()->create([
            'section_id' => $laptops->id,
            'name' => 'Product 1',
            'code' => 'product-1',
            'brand' => $brand->id,
            'active_from' => now()->subDay(),
            'preview_picture' => '/upload/iblock/1/1.jpg',
            'photos' => json_encode(['/upload/a.jpg']),
        ]);
        $product->seoMeta()->create(['title' => 'Buy product 1']);
        $cache = $this->app->make(RecordCache::class);

        foreach (['/catalog', '/catalog/computers/laptops/product-1'] as $url) {
            $this->get($url)->assertOk();
            $cache->reset();

            $this->get($url)->assertOk()->assertSee('Product 1');

            $this->assertSame(0, $cache->requestStats()['misses'], $url);
            $cache->reset();
        }
    }
}
