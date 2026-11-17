<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Support\PublicUrl;
use Atrium\Theme\Pages;
use Atrium\Theme\Tests\Fixtures\Brand;
use Atrium\Theme\Tests\Fixtures\Post;
use Atrium\Theme\Tests\Fixtures\Product;
use Atrium\Theme\Tests\Fixtures\ProductSection;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Which modules get pages from the theme, their addresses and the routes
 * drawn for them.
 */
final class ModulesTest extends TestCase
{
    /**
     * @return array<string,array{0:array<string,mixed>,1:list<string>}>
     */
    public static function publicUrls(): array
    {
        return [
            'a module with a prefix' => [[Brand::class => ['prefix' => 'brands']], ['brands']],
            'slashes around the prefix are dropped' => [[Brand::class => ['prefix' => '/brands/']], ['brands']],
            'a rule that is not a list' => [[Brand::class => 'brands'], []],
            'no prefix' => [[Brand::class => ['prefix' => '']], []],
            'a prefix of slashes only' => [[Brand::class => ['prefix' => '/']], []],
            'a rule without a prefix' => [[Brand::class => ['tree' => null]], []],
            'not a model' => [[Pages::class => ['prefix' => 'pages']], []],
            'no such class' => [['App\\Models\\Nothing' => ['prefix' => 'nothing']], []],
            'the longest prefix first, then by name' => [
                [Post::class => ['prefix' => 'posts'], Brand::class => ['prefix' => 'catalog/brands'], Product::class => ['prefix' => 'catalog'], ProductSection::class => ['prefix' => 'boards']],
                ['catalog/brands', 'catalog', 'boards', 'posts'],
            ],
        ];
    }

    /**
     * @param  array<string,mixed>  $rules
     * @param  list<string>  $prefixes
     */
    #[DataProvider('publicUrls')]
    public function test_only_models_with_a_prefix_get_pages(array $rules, array $prefixes): void
    {
        config(['atrium.public_urls' => $rules]);

        $this->assertSame($prefixes, $this->app->make(Pages::class)->prefixes());
    }

    public function test_a_module_keeps_every_model_of_its_prefix(): void
    {
        $this->assertSame([Product::class, ProductSection::class], array_keys($this->app->make(Pages::class)->module('catalog')));
        $this->assertSame([], $this->app->make(Pages::class)->module('nothing'));
    }

    public function test_a_record_without_a_code_or_a_module_has_no_address(): void
    {
        $pages = $this->app->make(Pages::class);

        $this->assertSame('lamp', $pages->path(Product::query()->create(['name' => 'Lamp', 'code' => 'lamp'])));
        $this->assertNull($pages->path(Product::query()->create(['name' => 'Lamp', 'code' => ''])));
        $this->assertNull($pages->path(Product::query()->create(['name' => 'Lamp', 'code' => null])));
        // A module the theme draws no pages for.
        $this->assertNull($pages->path(new Brand(['name' => 'Acme', 'code' => 'acme'])));
    }

    public function test_a_record_whose_section_is_gone_has_only_the_module_above_it(): void
    {
        Product::query()->create(['name' => 'Lamp', 'code' => 'lamp', 'section_id' => 999]);
        PublicUrl::forgetPaths();

        $html = (string) $this->get('/catalog/lamp')->assertOk()->assertSee('<h1>Lamp</h1>', false)->getContent();

        $this->assertSame(1, preg_match('#<nav class="breadcrumbs"[^>]*>(.*?)</nav>#s', $html, $nav));
        $this->assertSame(2, substr_count($nav[1], '<li>'));
        $this->assertStringContainsString('<li><a href="'.rtrim(url('/'), '/').'/catalog">Products</a></li>', $nav[1]);
    }

    public function test_the_theme_draws_the_home_page_and_the_module_pages(): void
    {
        $this->assertTrue(Route::has('atrium.theme.home'));
        $this->assertTrue(Route::has('atrium.theme.index'));
        $this->assertTrue(Route::has('atrium.theme.page'));
    }

    public function test_without_modules_only_the_home_page_is_drawn(): void
    {
        $this->assertTrue(Route::has('atrium.theme.home'));
        $this->assertFalse(Route::has('atrium.theme.index'));
        $this->assertFalse(Route::has('atrium.theme.page'));
        $this->get('/')->assertOk();
        $this->get('/catalog')->assertNotFound();
    }

    #[DefineEnvironment('withoutRoutes')]
    public function test_routes_off_leaves_every_address_to_the_app(): void
    {
        $this->assertFalse(Route::has('atrium.theme.home'));
        $this->assertFalse(Route::has('atrium.theme.index'));
        $this->assertFalse(Route::has('atrium.theme.page'));
        $this->get('/catalog')->assertNotFound();
    }

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        // After the parent's modules, which an attribute could not undo.
        if ($this->name() === 'test_without_modules_only_the_home_page_is_drawn') {
            $app['config']->set('atrium.public_urls', []);
        }
    }

    protected function withoutRoutes($app): void
    {
        $app['config']->set('atrium-theme.routes', false);
    }
}
