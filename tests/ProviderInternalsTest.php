<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Models\Site;
use Atrium\Admin\Support\PublicUrl;
use Atrium\Admin\Support\Sites;
use Atrium\Theme\Pages;
use Atrium\Theme\Tests\Fixtures\Brand;
use Atrium\Theme\Tests\Fixtures\Digest;
use Atrium\Theme\Tests\Fixtures\Post;
use Atrium\Theme\Tests\Fixtures\Story;
use Atrium\Theme\ThemeServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use ReflectionProperty;

/**
 * What the provider wires up: one list of pages, the publishable files, the
 * stylesheet, the routes of a prefix with a dot and the caches an edit of a
 * record has to clear.
 */
final class ProviderInternalsTest extends TestCase
{
    private const VERSION_KEY = 'atrium.sitemap.version';

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('atrium.public_urls', [
            Post::class => ['prefix' => 'posts'],
            Story::class => ['prefix' => 'stories.old'],
            Digest::class => ['prefix' => 'digests'],
        ]);
    }

    public function test_the_pages_are_one_object_for_the_whole_app(): void
    {
        $this->assertSame(app(Pages::class), app(Pages::class));
    }

    public function test_the_files_of_the_theme_can_be_published(): void
    {
        $dir = dirname(__DIR__);
        $paths = fn (string $tag): array => $this->resolved(ServiceProvider::pathsToPublish(ThemeServiceProvider::class, $tag));

        $this->assertSame([$dir.'/config/atrium-theme.php' => config_path('atrium-theme.php')], $paths('atrium-theme-config'));
        $this->assertSame([$dir.'/resources/views' => resource_path('views/vendor/atrium-theme')], $paths('atrium-theme-views'));
        $this->assertSame([$dir.'/resources/views/errors' => resource_path('views/errors')], $paths('atrium-errors'));
    }

    /**
     * @param  array<string,string>  $paths
     * @return array<string,string>
     */
    private function resolved(array $paths): array
    {
        $resolved = [];

        foreach ($paths as $from => $to) {
            $resolved[(string) realpath($from)] = $to;
        }

        return $resolved;
    }

    public function test_the_stylesheet_is_the_file_of_the_theme(): void
    {
        $css = (string) file_get_contents(dirname(__DIR__).'/resources/css/theme.css');

        $this->assertNotSame('', $css);
        $this->assertSame($css, ThemeServiceProvider::styles());
        $this->assertStringContainsString($css, (string) $this->get('/')->assertOk()->getContent());
    }

    public function test_a_dot_in_a_prefix_is_a_dot_in_the_route(): void
    {
        $route = Route::getRoutes()->getByName('atrium.theme.index');

        $this->assertSame('stories\.old|digests|posts', $route->wheres['prefix']);
        $this->assertSame('stories\.old|digests|posts', Route::getRoutes()->getByName('atrium.theme.page')->wheres['prefix']);
    }

    public function test_the_name_of_the_site_heads_the_pages(): void
    {
        Site::query()->where('code', 'main')->update(['name' => 'Main shop']);
        app(Sites::class)->flush();

        $this->get('/')->assertOk()->assertSee('<a class="site-name" href="'.$this->root().'">Main shop</a>', false);

        Site::query()->where('code', 'main')->update(['name' => '']);
        app(Sites::class)->flush();

        $this->get('/')->assertOk()->assertSee('<a class="site-name" href="'.$this->root().'">Demo shop</a>', false);
    }

    /**
     * @return array<string,array<int,mixed>>
     */
    private function warmPaths(): array
    {
        $cache = new ReflectionProperty(PublicUrl::class, 'pathCache');
        $cache->setValue(null, ['warm' => [1 => 'a']]);

        return $cache->getValue();
    }

    private function pathsAreWarm(): bool
    {
        return (new ReflectionProperty(PublicUrl::class, 'pathCache'))->getValue() !== [];
    }

    private function saved(object|string $record): void
    {
        Cache::forever(self::VERSION_KEY, 'old');
        $this->warmPaths();
        Event::dispatch('eloquent.saved: '.(is_object($record) ? $record::class : 'x'), [$record]);
    }

    public function test_saving_a_record_with_a_page_clears_the_sitemap_and_the_addresses(): void
    {
        $this->saved(new Post);

        $this->assertNotSame('old', Cache::get(self::VERSION_KEY));
        $this->assertFalse($this->pathsAreWarm());
    }

    public function test_deleting_a_record_with_a_page_clears_them_too(): void
    {
        Cache::forever(self::VERSION_KEY, 'old');
        $this->warmPaths();

        Event::dispatch('eloquent.deleted: '.Post::class, [new Post]);

        $this->assertNotSame('old', Cache::get(self::VERSION_KEY));
        $this->assertFalse($this->pathsAreWarm());
    }

    public function test_a_record_without_a_page_leaves_them(): void
    {
        $this->saved(new Brand);

        $this->assertSame('old', Cache::get(self::VERSION_KEY));
        $this->assertTrue($this->pathsAreWarm());
    }

    public function test_a_payload_that_is_no_record_leaves_them(): void
    {
        $this->saved('not a record');

        $this->assertSame('old', Cache::get(self::VERSION_KEY));
        $this->assertTrue($this->pathsAreWarm());
    }

    public function test_a_record_that_lists_itself_in_the_sitemap_is_left_to_its_own_flush(): void
    {
        $this->saved(new Digest);

        $this->assertTrue($this->pathsAreWarm());
    }
}
