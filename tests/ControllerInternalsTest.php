<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Models\Language;
use Atrium\Admin\Modules\Module;
use Atrium\Admin\Modules\Modules;
use Atrium\Admin\Modules\Settings;
use Atrium\Admin\Support\ContentLanguages;
use Atrium\Theme\Http\Controllers\PageController;
use Atrium\Theme\Tests\Fixtures\Accessory;
use Atrium\Theme\Tests\Fixtures\Crate;
use Atrium\Theme\Tests\Fixtures\Post;
use Atrium\Theme\Tests\Fixtures\Product;
use Atrium\Theme\Tests\Fixtures\ProductSection;
use Atrium\Theme\Tests\Fixtures\Shelf;
use Atrium\Theme\Tests\Fixtures\ShopSettings;
use Atrium\Theme\Tests\Fixtures\Spare;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * The pages of a module that has several models: which one names the
 * list, which one fills a section, how far the breadcrumbs climb and how
 * long a page of a list is.
 */
final class ControllerInternalsTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('atrium.public_urls', [
            Crate::class => ['prefix' => 'catalog', 'tree' => Crate::class, 'parent' => 'parent_id'],
            Accessory::class => ['prefix' => 'catalog', 'tree' => Crate::class, 'parent' => 'section_id'],
            Product::class => ['prefix' => 'catalog', 'tree' => ProductSection::class, 'parent' => 'section_id'],
            ProductSection::class => ['prefix' => 'catalog', 'tree' => ProductSection::class, 'parent' => 'parent_id'],
            Spare::class => ['prefix' => 'catalog', 'tree' => ProductSection::class, 'parent' => 'section_id'],
            Shelf::class => ['prefix' => 'shelves', 'tree' => Shelf::class, 'parent' => 'parent_id'],
            Post::class => ['prefix' => 'posts'],
        ]);
    }

    private function makePost(string $name): Post
    {
        return Post::query()->create(['name' => ['en' => $name], 'code' => ['en' => strtolower($name)]]);
    }

    private function language(): void
    {
        Language::query()->create(['code' => 'ru', 'name' => 'Russian']);
        app(ContentLanguages::class)->flush();
    }

    public function test_a_module_is_named_by_its_first_record_model_and_listed_by_it(): void
    {
        Crate::query()->create(['name' => 'Wooden', 'code' => 'wooden']);

        $this->get('/')->assertOk()->assertSee('Accessories')->assertDontSee('Crates');
        $this->get('/catalog')->assertOk()->assertSee('<h1>Accessories</h1>', false)->assertDontSee('Crates');
    }

    public function test_a_module_of_sections_only_is_named_by_its_first_section_model(): void
    {
        ProductSection::query()->create(['name' => 'Garden', 'code' => 'garden']);

        $this->get('/')->assertOk()->assertSee('Product sections');
        $this->get('/shelves')->assertOk()->assertSee('<h1>Product sections</h1>', false)->assertSee('Garden');
    }

    public function test_the_list_of_a_module_shows_the_top_sections_of_every_tree(): void
    {
        ProductSection::query()->create(['name' => 'Computers', 'code' => 'computers']);
        Crate::query()->create(['name' => 'Wooden', 'code' => 'wooden']);

        $this->get('/catalog')->assertOk()->assertSeeInOrder(['Wooden', 'Computers'])->assertDontSee('class="crates"', false);
    }

    public function test_the_home_page_shows_three_records_of_a_module_unless_told_otherwise(): void
    {
        foreach (['Alpha', 'Beta', 'Gamma', 'Delta'] as $name) {
            $this->makePost($name);
        }

        $this->get('/')->assertOk()->assertSeeInOrder(['Alpha', 'Beta', 'Gamma'])->assertDontSee('Delta');

        config(['atrium-theme.home_items' => 2]);
        $this->get('/')->assertOk()->assertSee('Beta')->assertDontSee('Gamma');
    }

    public function test_a_list_of_a_page_other_than_the_first_has_no_language_versions(): void
    {
        $this->language();

        $this->get('/catalog')->assertOk()->assertSee('<link rel="alternate" hreflang="ru"', false);
        $this->get('/catalog?page=2')->assertOk()->assertDontSee('<link rel="alternate"', false);
    }

    public function test_a_record_without_a_name_in_the_language_is_not_found(): void
    {
        Post::query()->create(['name' => ['en' => ''], 'code' => ['en' => 'blank']]);

        $this->get('/posts/blank')->assertNotFound();
    }

    public function test_the_actions_of_the_controller_are_public(): void
    {
        $controller = app(PageController::class);
        ProductSection::query()->create(['name' => 'Computers', 'code' => 'computers']);

        $this->assertInstanceOf(View::class, $controller->home());
        $this->assertInstanceOf(View::class, $controller->index(Request::create('/catalog'), 'catalog'));
        $this->assertInstanceOf(View::class, $controller->show(Request::create('/catalog/computers'), 'catalog', 'computers'));
    }

    public function test_slashes_around_the_path_do_not_send_the_visitor_elsewhere(): void
    {
        ProductSection::query()->create(['name' => 'Computers', 'code' => 'computers']);

        $response = app(PageController::class)->show(Request::create('/catalog/computers/'), 'catalog', 'computers/');

        $this->assertNotInstanceOf(RedirectResponse::class, $response);
    }

    public function test_a_section_shows_the_records_of_the_first_model_that_stands_in_its_tree(): void
    {
        $computers = ProductSection::query()->create(['name' => 'Computers', 'code' => 'computers']);
        Product::query()->create(['name' => 'Real product', 'code' => 'real', 'section_id' => $computers->id]);
        Accessory::query()->create(['name' => 'Stray accessory', 'code' => 'stray', 'section_id' => $computers->id]);
        Spare::query()->create(['name' => 'Second spare', 'code' => 'spare', 'section_id' => $computers->id]);

        $this->get('/catalog/computers')
            ->assertOk()
            ->assertSee('Real product')
            ->assertDontSee('Stray accessory')
            ->assertDontSee('Second spare');
    }

    public function test_the_pages_of_a_section_are_pages_of_their_own_and_garbage_is_the_first(): void
    {
        config(['atrium-theme.per_page' => 1]);
        $computers = ProductSection::query()->create(['name' => 'Computers', 'code' => 'computers']);
        Product::query()->create(['name' => 'First', 'code' => 'first', 'section_id' => $computers->id]);
        Product::query()->create(['name' => 'Second', 'code' => 'second', 'section_id' => $computers->id]);
        $canonical = fn (string $url): string => '<link rel="canonical" href="'.$this->root().$url.'">';

        $this->get('/catalog/computers')->assertSee($canonical('/catalog/computers'), false);
        $this->get('/catalog/computers?page=2')->assertSee($canonical('/catalog/computers?page=2'), false);
        $this->get('/catalog/computers?page=abc')->assertSee($canonical('/catalog/computers'), false);
        $this->get('/catalog?page=abc')->assertSee($canonical('/catalog'), false);
    }

    private function crumbs(string $uri): int
    {
        $html = (string) $this->get($uri)->assertOk()->getContent();
        preg_match('#<nav class="breadcrumbs".*?</nav>#s', $html, $nav);

        return substr_count($nav[0] ?? '', '<li>');
    }

    public function test_the_breadcrumbs_climb_twenty_sections_at_most(): void
    {
        $parent = null;

        foreach (range(1, 25) as $depth) {
            $parent = ProductSection::query()->create(['name' => "Level {$depth}", 'code' => "level-{$depth}", 'parent_id' => $parent?->id]);
        }

        Product::query()->create(['name' => 'Deep', 'code' => 'deep', 'section_id' => $parent?->id]);
        // The address of a record keeps the twenty nearest sections.
        $path = implode('/', array_map(fn (int $depth): string => "level-{$depth}", range(6, 25)));

        // The site, the list, and the twenty nearest sections.
        $this->assertSame(22, $this->crumbs("/catalog/{$path}/deep"));
    }

    public function test_the_breadcrumbs_of_a_few_sections_are_all_of_them(): void
    {
        $parent = null;

        foreach (range(1, 3) as $depth) {
            $parent = ProductSection::query()->create(['name' => "Level {$depth}", 'code' => "level-{$depth}", 'parent_id' => $parent?->id]);
        }

        Product::query()->create(['name' => 'Deep', 'code' => 'deep', 'section_id' => $parent?->id]);

        $this->assertSame(5, $this->crumbs('/catalog/level-1/level-2/level-3/deep'));
    }

    /**
     * @return array<int,string>
     */
    private function treeQueries(string $uri): array
    {
        DB::enableQueryLog();
        $this->get($uri)->assertOk();
        $queries = array_column(DB::getQueryLog(), 'query');
        DB::disableQueryLog();

        return array_values(array_filter($queries, fn (string $sql): bool => str_contains($sql, 'product_sections') && str_ends_with($sql, 'limit 1')));
    }

    public function test_a_record_that_stands_in_no_section_asks_the_tree_nothing(): void
    {
        Product::query()->create(['name' => 'Gift card', 'code' => 'gift-card']);

        $this->assertSame([], $this->treeQueries('/catalog/gift-card'));
    }

    public function test_a_record_whose_section_is_gone_asks_the_tree_once(): void
    {
        Product::query()->create(['name' => 'Orphan', 'code' => 'orphan', 'section_id' => 999]);

        $this->assertCount(1, $this->treeQueries('/catalog/orphan'));
    }

    public function test_a_list_has_twenty_records_a_page_by_default(): void
    {
        foreach (range(1, 21) as $number) {
            $this->makePost('Post '.$number);
        }

        $html = (string) $this->get('/posts')->assertOk()->assertSee('Page 1 of 2')->getContent();
        $this->assertSame(20, substr_count($html, '<li class="card">'));
    }

    public function test_a_page_of_a_list_that_cannot_be_counted_has_the_default_of_the_model(): void
    {
        config(['atrium-theme.per_page' => 'abc']);

        foreach (range(1, 16) as $number) {
            $this->makePost('Post '.$number);
        }

        $html = (string) $this->get('/posts')->assertOk()->assertSee('Page 1 of 2')->getContent();
        $this->assertSame(15, substr_count($html, '<li class="card">'));
    }

    private function shopPerPage(string $value): void
    {
        app(Modules::class)->add(new Module(name: 'alveby/atrium-shop', resources: [Post::class], key: 'shop'));

        $settings = app(Settings::class);
        $set = function (string $property, array $values) use ($settings): void {
            $reflection = new \ReflectionProperty($settings, $property);
            $reflection->setValue($settings, $values);
        };
        $set('declarations', ['shop' => new ShopSettings]);
        $set('stored', ['shop' => [0 => ['per_page' => $value]]]);
    }

    public function test_the_module_decides_how_many_records_a_page_has(): void
    {
        $this->shopPerPage('2');

        foreach (range(1, 5) as $number) {
            $this->makePost('Post '.$number);
        }

        $html = (string) $this->get('/posts')->assertOk()->assertSee('Page 1 of 3')->getContent();
        $this->assertSame(2, substr_count($html, '<li class="card">'));
    }

    public function test_a_setting_nobody_filled_in_leaves_the_theme_default(): void
    {
        $this->shopPerPage('abc');

        foreach (range(1, 5) as $number) {
            $this->makePost('Post '.$number);
        }

        $html = (string) $this->get('/posts')->assertOk()->getContent();
        $this->assertSame(5, substr_count($html, '<li class="card">'));
        $this->assertStringNotContainsString('Page 1 of', $html);
    }
}
