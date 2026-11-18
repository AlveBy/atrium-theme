<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\AtriumServiceProvider;
use Atrium\Admin\Support\PublicUrl;
use Atrium\Theme\Tests\Fixtures\Post;
use Atrium\Theme\Tests\Fixtures\Product;
use Atrium\Theme\Tests\Fixtures\ProductSection;
use Atrium\Theme\ThemeServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\ServiceProvider as InertiaServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Tighten\Ziggy\ZiggyServiceProvider;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            InertiaServiceProvider::class,
            ZiggyServiceProvider::class,
            AtriumServiceProvider::class,
            ThemeServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $config = $app['config'];
        $config->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        $config->set('app.name', 'Demo shop');
        // CI runs the suite against MySQL and PostgreSQL too: the lookups by
        // a translated code are JSON queries, different on each.
        $target = env('DB_CONNECTION');
        $config->set('database.default', in_array($target, ['mysql', 'pgsql'], true) ? $target : 'testing');
        // Recording a page's timing prunes old rows on one request in a
        // hundred: an extra query the query-counting tests must not see.
        $config->set('atrium.dashboard.timings.enabled', false);
        $config->set('atrium.seo.title_suffix', ' | Demo shop');
        $config->set('atrium.public_urls', [
            Product::class => ['prefix' => 'catalog', 'tree' => ProductSection::class, 'parent' => 'section_id'],
            ProductSection::class => ['prefix' => 'catalog', 'tree' => ProductSection::class, 'parent' => 'parent_id'],
            Post::class => ['prefix' => 'posts'],
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        PublicUrl::forgetPaths();
    }

    /**
     * The site's address as the test requests see it (the root app's .env
     * may set another one when the whole suite runs).
     */
    protected function root(): string
    {
        return rtrim(url('/'), '/');
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }
}
