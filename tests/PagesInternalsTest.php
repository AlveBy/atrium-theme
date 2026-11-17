<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Models\Language;
use Atrium\Admin\Support\ContentLanguages;
use Atrium\Admin\Support\PublicUrl;
use Atrium\Theme\Pages;
use Atrium\Theme\Tests\Fixtures\Brand;
use Atrium\Theme\Tests\Fixtures\Numbered;
use Atrium\Theme\Tests\Fixtures\Post;
use Atrium\Theme\Tests\Fixtures\Product;
use Atrium\Theme\Tests\Fixtures\ProductSection;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use stdClass;

/**
 * How the theme finds, orders and addresses the records of a module.
 */
final class PagesInternalsTest extends TestCase
{
    private Pages $pages;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pages = new Pages;
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_what_is_not_a_model_or_has_no_prefix_is_skipped_and_the_next_one_is_not(): void
    {
        config(['atrium.public_urls' => [
            stdClass::class => ['prefix' => 'std'],
            Brand::class => ['prefix' => '/'],
            Post::class => ['prefix' => 'posts'],
        ]]);

        $this->assertSame(['posts'], $this->pages->prefixes());
        $this->assertSame([Post::class], array_keys($this->pages->module('posts')));
        $this->assertSame([], $this->pages->module('std'));
    }

    public function test_the_models_of_a_prefix_are_told_apart_into_sections_and_records(): void
    {
        $this->assertSame([Product::class], $this->pages->elementModels('catalog'));
        $this->assertSame([ProductSection::class], $this->pages->sectionModels('catalog'));
        $this->assertSame([Post::class], $this->pages->elementModels('posts'));
        $this->assertSame([], $this->pages->sectionModels('posts'));
    }

    public function test_a_label_is_the_one_the_admin_shows_or_the_name_of_the_class(): void
    {
        $this->assertSame('Products', $this->pages->label(Product::class));
        $this->assertSame('Brands', $this->pages->label(Brand::class));
        $this->assertSame('Std Classes', $this->pages->label(stdClass::class));
        $this->assertSame('7', $this->pages->label(Numbered::class));
    }

    private function language(): void
    {
        Language::query()->create(['code' => 'ru', 'name' => 'Russian']);
        app(ContentLanguages::class)->flush();
    }

    /**
     * @return array<int,string>
     */
    private function visible(string $class = Product::class): array
    {
        return $this->pages->visible($class)->pluck('name')->all();
    }

    public function test_records_come_by_sort_then_the_newest_then_the_order_they_were_made_in(): void
    {
        Product::query()->create(['name' => 'Third', 'code' => 'third', 'sort' => 300]);
        Product::query()->create(['name' => 'First', 'code' => 'first', 'sort' => 100]);
        Product::query()->create(['name' => 'Old', 'code' => 'old', 'sort' => 200, 'active_from' => '2026-01-01 10:00:00']);
        Product::query()->create(['name' => 'New', 'code' => 'new', 'sort' => 200, 'active_from' => '2026-02-01 10:00:00']);
        Product::query()->create(['name' => 'Twin A', 'code' => 'twin-a', 'sort' => 400, 'active_from' => '2026-03-01 10:00:00']);
        Product::query()->create(['name' => 'Twin B', 'code' => 'twin-b', 'sort' => 400, 'active_from' => '2026-03-01 10:00:00']);

        $this->assertSame(['First', 'New', 'Old', 'Third', 'Twin A', 'Twin B'], $this->visible());
    }

    public function test_a_record_is_visible_while_it_is_active_and_inside_its_period(): void
    {
        Carbon::setTestNow('2026-06-01 12:00:00');

        Product::query()->create(['name' => 'Always', 'code' => 'always']);
        Product::query()->create(['name' => 'Inactive', 'code' => 'inactive', 'active' => false]);
        Product::query()->create(['name' => 'Not yet', 'code' => 'not-yet', 'active_from' => '2026-06-01 12:00:01']);
        Product::query()->create(['name' => 'Starts now', 'code' => 'starts-now', 'active_from' => '2026-06-01 12:00:00']);
        Product::query()->create(['name' => 'Ended', 'code' => 'ended', 'active_to' => '2026-06-01 11:59:59']);
        Product::query()->create(['name' => 'Ends now', 'code' => 'ends-now', 'active_to' => '2026-06-01 12:00:00']);
        Product::query()->create(['name' => 'Ends later', 'code' => 'ends-later', 'active_to' => '2026-07-01 12:00:00']);

        $names = $this->visible();
        sort($names);

        $this->assertSame(['Always', 'Ends later', 'Ends now', 'Starts now'], $names);
    }

    public function test_a_model_without_the_columns_is_ordered_by_its_key_only(): void
    {
        Brand::query()->create(['name' => 'Zed', 'xml_id' => 'z']);
        Brand::query()->create(['name' => 'Alpha', 'xml_id' => 'a']);

        $this->assertSame(['Zed', 'Alpha'], $this->visible(Brand::class));
    }

    private function twins(): void
    {
        $computers = ProductSection::query()->create(['name' => 'Computers', 'code' => 'computers']);
        $laptops = ProductSection::query()->create(['name' => 'Laptops', 'code' => 'laptops', 'parent_id' => $computers->id]);

        Product::query()->create(['name' => 'Desktop twin', 'code' => 'twin', 'section_id' => $computers->id]);
        Product::query()->create(['name' => 'Laptop twin', 'code' => 'twin', 'section_id' => $laptops->id]);
    }

    public function test_the_exact_address_wins_wherever_the_record_stands_among_the_candidates(): void
    {
        $this->twins();

        $this->assertSame('Laptop twin', $this->pages->find('catalog', 'computers/laptops/twin')?->getAttribute('name'));
        $this->assertSame('Desktop twin', $this->pages->find('catalog', 'computers/twin')?->getAttribute('name'));
    }

    public function test_failing_the_exact_address_the_first_record_of_the_code_is_found(): void
    {
        $this->twins();

        $this->assertSame('Desktop twin', $this->pages->find('catalog', 'nowhere/twin')?->getAttribute('name'));
        $this->assertSame('Desktop twin', $this->pages->find('catalog', 'twin')?->getAttribute('name'));
        $this->assertNull($this->pages->find('catalog', 'nowhere/missing'));
    }

    public function test_slashes_around_an_address_are_not_part_of_it(): void
    {
        $this->twins();

        $this->assertSame('Laptop twin', $this->pages->find('catalog', '/computers/laptops/twin/')?->getAttribute('name'));
        $this->assertSame('Desktop twin', $this->pages->find('catalog', 'twin/')?->getAttribute('name'));
    }

    public function test_a_record_in_no_section_is_addressed_by_its_code_and_asks_the_tree_nothing(): void
    {
        config(['atrium.public_urls.'.Brand::class => ['prefix' => 'brands', 'tree' => ProductSection::class]]);
        PublicUrl::forgetPaths();
        $brand = Brand::query()->create(['name' => 'Northwind', 'xml_id' => 'northwind']);
        $brand->setAttribute('code', 'northwind');

        DB::enableQueryLog();
        $path = $this->pages->path($brand);
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $this->assertSame('northwind', $path);
        $this->assertSame([], $queries);
    }

    public function test_a_record_without_a_rule_or_a_code_has_no_address(): void
    {
        $brand = Brand::query()->create(['name' => 'Northwind', 'xml_id' => 'northwind']);
        $this->assertNull($this->pages->path($brand));
        $this->assertNull($this->pages->url($brand));

        $product = Product::query()->create(['name' => 'No code']);
        $this->assertNull($this->pages->path($product));
        $this->assertNull($this->pages->url($product));
    }

    public function test_a_record_that_is_not_translated_has_a_page_even_with_an_empty_name(): void
    {
        $product = Product::query()->create(['name' => '', 'code' => 'nameless']);

        $this->assertSame($this->root().'/catalog/nameless', $this->pages->url($product));
    }

    public function test_a_translated_record_has_a_page_only_in_the_languages_with_its_name(): void
    {
        $this->language();
        $post = Post::query()->create(['name' => ['en' => 'Hello', 'ru' => ''], 'code' => ['en' => 'hello', 'ru' => 'privet']]);

        $this->assertSame($this->root().'/posts/hello', $this->pages->url($post, 'en'));
        $this->assertNull($this->pages->url($post, 'ru'));
        $this->assertSame(['en' => $this->root().'/posts/hello'], $this->pages->urls($post));
    }

    public function test_a_record_that_is_not_translated_has_one_page_in_the_language_of_the_visitor(): void
    {
        $this->language();
        $product = Product::query()->create(['name' => 'Gift card', 'code' => 'gift-card']);

        $this->assertSame(['en' => $this->root().'/catalog/gift-card'], $this->pages->urls($product));

        app()->setLocale('ru');
        $this->assertSame(['ru' => $this->root().'/ru/catalog/gift-card'], $this->pages->urls($product));
    }

    public function test_a_translated_record_has_a_page_in_each_language(): void
    {
        $this->language();
        $post = Post::query()->create(['name' => ['en' => 'Hello', 'ru' => 'Привет'], 'code' => ['en' => 'hello', 'ru' => 'privet']]);

        $this->assertSame([
            'en' => $this->root().'/posts/hello',
            'ru' => $this->root().'/ru/posts/privet',
        ], $this->pages->urls($post));
    }

    public function test_a_text_is_always_a_string(): void
    {
        $product = Product::query()->create(['name' => 'Laptop', 'code' => 'laptop', 'sort' => 100]);

        $this->assertSame('100', $this->pages->text($product, 'sort'));
        $this->assertSame('Laptop', $this->pages->text($product, 'name'));
        $this->assertSame('', $this->pages->text($product, 'missing'));
        $this->assertSame('', $this->pages->text($product->setAttribute('photos', ['a']), 'photos'));
    }

    public function test_the_columns_of_a_table_are_read_once_and_per_connection(): void
    {
        config(['database.connections.other' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
        Schema::connection('other')->create('products', function ($table): void {
            $table->id();
            $table->string('title');
        });
        $other = new class extends Model
        {
            protected $connection = 'other';

            protected $table = 'products';
        };

        DB::enableQueryLog();
        $mine = $this->pages->columns(new Product);
        $first = count(DB::getQueryLog());
        $this->pages->columns(new Product);
        $again = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertArrayHasKey('active', $mine);
        $this->assertGreaterThan(0, $first);
        $this->assertSame($first, $again);
        $this->assertSame(['id' => true, 'title' => true], $this->pages->columns($other));
        $this->assertArrayHasKey('active', $this->pages->columns(new Product));
        $this->assertSame(['|products', 'other|products'], array_keys((new \ReflectionProperty($this->pages, 'columns'))->getValue($this->pages)));
    }
}
