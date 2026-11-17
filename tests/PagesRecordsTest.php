<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Cache\RecordCache;
use Atrium\Admin\Support\PublicUrl;
use Atrium\Theme\Pages;
use Atrium\Theme\RecordView;
use Atrium\Theme\Tests\Fixtures\Post;
use Atrium\Theme\Tests\Fixtures\Product;
use Atrium\Theme\Tests\Fixtures\ProductSection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * The records of a query as pages: from their cards for a cached module
 * (no query when the cache is warm), from the database for any other,
 * and a record found by its code keeps the card it came from.
 */
final class PagesRecordsTest extends TestCase
{
    private function pages(): Pages
    {
        return $this->app->make(Pages::class);
    }

    /**
     * @return array<int,string>
     */
    private function titles(callable $records): array
    {
        return array_map(fn (RecordView $view): string => $view->title(), $records());
    }

    private function queries(callable $callback): int
    {
        $count = 0;
        DB::listen(function () use (&$count): void {
            $count++;
        });
        $callback();

        return $count;
    }

    public function test_the_records_of_a_cached_module_come_from_the_cache_with_no_query_once_it_is_warm(): void
    {
        $section = ProductSection::query()->create(['name' => 'Laptops', 'code' => 'laptops']);
        Product::query()->create(['name' => 'Alpha', 'code' => 'alpha', 'section_id' => $section->id]);
        Product::query()->create(['name' => 'Beta', 'code' => 'beta', 'section_id' => $section->id]);
        $this->pages()->records($this->pages()->visible(Product::class));
        $query = $this->pages()->visible(Product::class);
        $titles = [];

        $queries = $this->queries(function () use ($query, &$titles): void {
            $titles = $this->titles(fn (): array => $this->pages()->records($query));
        });

        $this->assertSame(['Alpha', 'Beta'], $titles);
        $this->assertSame(0, $queries);
    }

    public function test_the_records_of_a_cached_module_are_read_from_the_database_when_the_cache_is_off(): void
    {
        $section = ProductSection::query()->create(['name' => 'Laptops', 'code' => 'laptops']);
        Product::query()->create(['name' => 'Alpha', 'code' => 'alpha', 'section_id' => $section->id]);
        config(['atrium.cache.enabled' => false]);
        $query = $this->pages()->visible(Product::class);
        $titles = [];

        $queries = $this->queries(function () use ($query, &$titles): void {
            $titles = $this->titles(fn (): array => $this->pages()->records($query));
        });

        $this->assertSame(['Alpha'], $titles);
        $this->assertSame(1, $queries);
    }

    public function test_the_records_of_a_module_that_is_not_cached_are_read_from_the_database(): void
    {
        Post::query()->create(['name' => ['en' => 'Alpha'], 'code' => ['en' => 'alpha']]);
        Post::query()->create(['name' => ['en' => 'Beta'], 'code' => ['en' => 'beta']]);

        $records = $this->pages()->records($this->pages()->visible(Post::class));

        $this->assertSame(['Alpha', 'Beta'], array_map(fn (RecordView $view): string => $view->title(), $records));
    }

    public function test_one_record_of_a_module_that_is_not_cached_is_read_from_the_database(): void
    {
        $post = Post::query()->create(['name' => ['en' => 'Alpha'], 'code' => ['en' => 'alpha']]);

        $this->assertSame('Alpha', $this->pages()->record(Post::class, $post->id)?->title());
        $this->assertNull($this->pages()->record(Post::class, $post->id + 1));
    }

    public function test_one_record_of_a_cached_module_is_read_from_its_card(): void
    {
        $product = Product::query()->create(['name' => 'Alpha', 'code' => 'alpha']);
        $this->pages()->record(Product::class, $product->id);

        $queries = 0;
        DB::listen(function () use (&$queries): void {
            $queries++;
        });
        $found = $this->pages()->record(Product::class, $product->id);

        $this->assertSame('Alpha', $found?->title());
        $this->assertSame(0, $queries);
        $this->assertNull($this->pages()->record(Product::class, $product->id + 1));
    }

    public function test_a_record_found_by_its_code_keeps_the_card_it_came_from_even_when_the_address_is_another(): void
    {
        $computers = ProductSection::query()->create(['name' => 'Computers', 'code' => 'computers']);
        $phones = ProductSection::query()->create(['name' => 'Phones', 'code' => 'phones']);
        $first = Product::query()->create(['name' => 'First', 'code' => 'twin', 'section_id' => $computers->id]);
        $second = Product::query()->create(['name' => 'Second', 'code' => 'twin', 'section_id' => $phones->id]);
        // A card that says what the record could not: whose card it is.
        $this->app->make(RecordCache::class)->extend('theme', fn (Collection $models): array => $models->mapWithKeys(
            fn (Model $record): array => [$record->getKey() => ['title' => 'Card of '.$record->getAttribute('name')] + (new RecordView($record, $this->pages()))->toArray()],
        )->all());
        $this->app->make(RecordCache::class)->flushAll();
        PublicUrl::forgetPaths();

        $found = $this->pages()->find('catalog', 'nowhere/twin');

        $this->assertSame($first->id, $found?->getKey());
        $this->assertSame('Card of First', $this->pages()->page($found)->title());
        $this->assertNotSame($second->id, $found->getKey());
    }
}
