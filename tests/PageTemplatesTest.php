<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Templates\Templates;
use Atrium\Theme\Tests\Fixtures\Post;
use Atrium\Theme\Tests\Fixtures\Shelf;
use Atrium\Theme\Tests\Fixtures\Story;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\View;

/**
 * A record or a section drawn with the template the editor picked: the
 * theme's own "Full width" for any module, a template of the site or of a
 * skin, and the plain page when there is no template or its file is gone.
 */
final class PageTemplatesTest extends TestCase
{
    private string $views;

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('atrium.public_urls', [
            Post::class => ['prefix' => 'posts'],
            Shelf::class => ['prefix' => 'shelves', 'tree' => Shelf::class, 'parent' => 'parent_id'],
            Story::class => ['prefix' => 'stories'],
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->views = sys_get_temp_dir().'/atrium-theme-templates-'.uniqid();
        mkdir($this->views, 0777, true);
        view()->addLocation($this->views);
    }

    protected function tearDown(): void
    {
        (new Filesystem)->deleteDirectory($this->views);

        parent::tearDown();
    }

    private function hello(): Post
    {
        return Post::query()->create(['name' => ['en' => 'Hello'], 'code' => ['en' => 'hello'], 'detail_text' => ['en' => '<p>Body text.</p>']]);
    }

    private function template(string $path, string $contents): void
    {
        $file = $this->views.'/'.$path.'.blade.php';
        mkdir(dirname($file), 0777, true);
        file_put_contents($file, $contents);
        app(Templates::class)->forget();
    }

    public function test_a_record_without_a_template_keeps_the_usual_page(): void
    {
        $this->hello();

        $this->get('/posts/hello')
            ->assertOk()
            ->assertViewIs('atrium-theme::page')
            ->assertSee('<main id="content" class="wrap">', false)
            ->assertSee('<h1>Hello</h1>', false);
    }

    public function test_full_width_draws_the_same_page_across_the_window(): void
    {
        $post = $this->hello();
        app(Templates::class)->pick($post, 'full-width');

        $this->get('/posts/hello')
            ->assertOk()
            ->assertViewIs('atrium-theme::templates.all.full-width')
            ->assertSee('<main id="content" class="wrap-full">', false)
            ->assertDontSee('<main id="content" class="wrap">', false)
            ->assertSee('<h1>Hello</h1>', false)
            ->assertSee('<p>Body text.</p>', false);
    }

    public function test_full_width_is_for_every_module_sections_included(): void
    {
        $shelf = Shelf::query()->create(['name' => 'Garden', 'code' => 'garden']);

        $this->get('/shelves/garden')->assertOk()->assertSee('<main id="content" class="wrap">', false);

        app(Templates::class)->pick($shelf, 'full-width');

        $this->get('/shelves/garden')
            ->assertOk()
            ->assertViewIs('atrium-theme::templates.all.full-width')
            ->assertViewHas('defaultView', 'atrium-theme::list')
            ->assertSee('<main id="content" class="wrap-full">', false);
    }

    public function test_a_template_of_the_site_gets_what_the_usual_page_gets(): void
    {
        $post = $this->hello();
        $this->template('templates/posts/plain', "{{-- Template: Plain --}}\n@extends(\$defaultView)\n@section('main-class', 'mine')");
        app(Templates::class)->pick($post, 'plain');

        $this->get('/posts/hello')
            ->assertOk()
            ->assertViewIs('templates.posts.plain')
            ->assertViewHas('defaultView', 'atrium-theme::page')
            ->assertViewHasAll(['page', 'canonical', 'breadcrumbs', 'alternates', 'switch'])
            ->assertSee('<main id="content" class="mine">', false)
            ->assertSee('<h1>Hello</h1>', false);
    }

    public function test_the_modules_own_view_is_what_a_template_extends(): void
    {
        View::addNamespace('theme-test', __DIR__.'/Fixtures/views');
        $story = Story::query()->create(['name' => ['en' => 'Once upon a time'], 'code' => ['en' => 'once']]);
        app(Templates::class)->pick($story, 'full-width');

        $this->get('/stories/once')
            ->assertOk()
            ->assertViewIs('atrium-theme::templates.all.full-width')
            ->assertViewHas('defaultView', 'theme-test::story')
            ->assertSee('<h1 class="story">Once upon a time</h1>', false)
            ->assertSee('<main id="content" class="wrap-full">', false);
    }

    public function test_a_template_whose_file_was_deleted_is_drawn_as_the_usual_page(): void
    {
        $post = $this->hello();
        $this->template('templates/posts/plain', '@extends($defaultView)');
        app(Templates::class)->pick($post, 'plain');
        $this->get('/posts/hello')->assertOk()->assertViewIs('templates.posts.plain');

        unlink($this->views.'/templates/posts/plain.blade.php');
        view()->getFinder()->flush();
        app(Templates::class)->forget();

        $this->get('/posts/hello')
            ->assertOk()
            ->assertViewIs('atrium-theme::page')
            ->assertSee('<h1>Hello</h1>', false);
    }

    public function test_the_choice_of_a_record_is_not_the_choice_of_another(): void
    {
        $first = $this->hello();
        Post::query()->create(['name' => ['en' => 'Second'], 'code' => ['en' => 'second']]);
        app(Templates::class)->pick($first, 'full-width');

        $this->get('/posts/second')->assertOk()->assertViewIs('atrium-theme::page');
        $this->get('/posts/hello')->assertOk()->assertViewIs('atrium-theme::templates.all.full-width');
    }

    public function test_a_template_of_the_current_skin_comes_after_the_site_s_and_before_the_theme_s(): void
    {
        config(['atrium-theme.skin' => 'shop']);
        $post = $this->hello();
        $skin = $this->views.'/skin-views';
        mkdir($skin.'/skins/shop/templates/posts', 0777, true);
        file_put_contents($skin.'/skins/shop/templates/posts/skinned.blade.php', "{{-- Template: Skinned --}}\n@extends(\$defaultView)\n@section('main-class', 'skinned')");
        View::prependNamespace('atrium-theme', $skin);
        app(Templates::class)->forget();

        $this->assertArrayHasKey('skinned', app(Templates::class)->available(Post::class));
        $this->assertArrayHasKey('full-width', app(Templates::class)->available(Post::class));

        app(Templates::class)->pick($post, 'skinned');
        $this->get('/posts/hello')
            ->assertOk()
            ->assertViewIs('atrium-theme::skins.shop.templates.posts.skinned')
            ->assertSee('<main id="content" class="skinned">', false);
    }

    public function test_a_skin_of_another_name_does_not_offer_this_skin_s_templates(): void
    {
        config(['atrium-theme.skin' => 'clinic']);
        $skin = $this->views.'/skin-views';
        mkdir($skin.'/skins/shop/templates/posts', 0777, true);
        file_put_contents($skin.'/skins/shop/templates/posts/skinned.blade.php', 'x');
        View::prependNamespace('atrium-theme', $skin);
        app(Templates::class)->forget();

        $this->assertArrayNotHasKey('skinned', app(Templates::class)->available(Post::class));
    }

    public function test_without_a_skin_only_the_themes_own_folder_is_looked_in(): void
    {
        config(['atrium-theme.skin' => null]);
        app(Templates::class)->forget();

        $this->assertSame(['full-width'], array_keys(app(Templates::class)->available(Post::class)));
        $this->assertSame('Full width', app(Templates::class)->available(Post::class)['full-width']->label);
    }
}
