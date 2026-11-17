<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Theme\Pages;
use Atrium\Theme\Tests\Fixtures\Post;
use Atrium\Theme\Tests\Fixtures\ProductSection;
use Atrium\Theme\Tests\Fixtures\Story;
use Illuminate\Support\Facades\View;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * A module may draw its pages with a view of its own (themeView).
 */
final class ModuleViewTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('atrium.public_urls.'.Story::class, ['prefix' => 'stories']);
    }

    public function test_the_module_view_draws_the_page_inside_the_theme(): void
    {
        View::addNamespace('theme-test', __DIR__.'/Fixtures/views');
        Story::query()->create(['name' => ['en' => 'Once upon a time'], 'code' => ['en' => 'once']]);

        $response = $this->get('/stories/once')
            ->assertOk()
            ->assertSee('<h1 class="story">Once upon a time</h1>', false)
            ->assertSee('<link rel="canonical" href="'.$this->root().'/stories/once">', false);

        $content = (string) $response->getContent();
        $this->assertLessThan(strpos($content, '</head>'), strpos($content, '<meta name="story" content="Once upon a time">'));

        // A view that does not exist leaves the theme's own.
        $this->get('/stories')->assertOk()->assertSee('<h1>Stories</h1>', false);
    }

    public function test_a_section_without_a_module_view_keeps_the_themes_own(): void
    {
        View::addNamespace('theme-test', __DIR__.'/Fixtures/views');
        // Product sections have no themeView: the theme draws them.
        ProductSection::query()->create(['name' => 'Computers', 'code' => 'computers']);
        $this->get('/catalog/computers')->assertOk()->assertSee('<h1>Computers</h1>', false)->assertDontSee('class="shelf"', false);
    }

    /**
     * @return array<string,array{class-string,string,string}>
     */
    public static function views(): array
    {
        return [
            'the module names an existing view' => [Story::class, 'page', 'theme-test::story'],
            'the module names a view that does not exist' => [Story::class, 'index', 'default'],
            'the module has no view for this page' => [Story::class, 'section', 'default'],
            'the module has no themeView at all' => [Post::class, 'page', 'default'],
            'a class that is not a module' => [\stdClass::class, 'page', 'default'],
        ];
    }

    /**
     * @param  class-string  $class
     */
    #[DataProvider('views')]
    public function test_the_view_of_a_page_is_the_modules_when_it_has_one_and_exists(string $class, string $page, string $expected): void
    {
        View::addNamespace('theme-test', __DIR__.'/Fixtures/views');

        $this->assertSame($expected, (new Pages)->view($class, $page, 'default'));
    }
}
