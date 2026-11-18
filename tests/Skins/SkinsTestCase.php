<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Skins;

use Atrium\Admin\AtriumServiceProvider;
use Atrium\Admin\Support\PublicUrl;
use Atrium\Brands\BrandsServiceProvider;
use Atrium\Catalog\CatalogServiceProvider;
use Atrium\Faq\FaqServiceProvider;
use Atrium\Forms\FormsServiceProvider;
use Atrium\News\NewsServiceProvider;
use Atrium\Pages\PagesServiceProvider;
use Atrium\Reviews\ReviewsServiceProvider;
use Atrium\Stores\StoresServiceProvider;
use Atrium\Theme\Tests\Skins\Fixtures\SkinContent;
use Atrium\Theme\ThemeServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\Storage;
use Inertia\ServiceProvider as InertiaServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Tighten\Ziggy\ZiggyServiceProvider;

/**
 * The skins on the real modules: a class says which of them are installed.
 */
abstract class SkinsTestCase extends Orchestra
{
    use RefreshDatabase;

    /**
     * Every module a skin's home page can show.
     */
    protected const ALL_MODULES = [
        'alveby/atrium-catalog', 'alveby/atrium-brands', 'alveby/atrium-stores', 'alveby/atrium-news',
        'alveby/atrium-reviews', 'alveby/atrium-faq', 'alveby/atrium-forms', 'alveby/atrium-pages',
    ];

    /**
     * @return array<int,string>
     */
    abstract protected function modules(): array;

    /**
     * On MySQL and PostgreSQL the whole suite shares one database: each
     * class migrates it again with the modules it installs.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        RefreshDatabaseState::$migrated = false;
    }

    public static function tearDownAfterClass(): void
    {
        RefreshDatabaseState::$migrated = false;
        parent::tearDownAfterClass();
    }

    /**
     * @return array<string,array{string}>
     */
    public static function skins(): array
    {
        return ['travel' => ['travel'], 'restaurant' => ['restaurant'], 'clinic' => ['clinic'], 'shop' => ['shop']];
    }

    protected function getPackageProviders($app): array
    {
        return [
            InertiaServiceProvider::class,
            ZiggyServiceProvider::class,
            AtriumServiceProvider::class,
            ThemeServiceProvider::class,
            CatalogServiceProvider::class,
            BrandsServiceProvider::class,
            StoresServiceProvider::class,
            NewsServiceProvider::class,
            ReviewsServiceProvider::class,
            FaqServiceProvider::class,
            FormsServiceProvider::class,
            PagesServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $config = $app['config'];
        $config->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        $config->set('app.name', 'Demo site');
        $target = env('DB_CONNECTION');
        $config->set('database.default', in_array($target, ['mysql', 'pgsql'], true) ? $target : 'testing');
        $config->set('atrium.modules', $this->modules());
        $config->set('atrium.auth.two_factor.enabled', false);
        $config->set('atrium.dashboard.timings.enabled', false);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        PublicUrl::forgetPaths();
    }

    /**
     * The skin's content (Fixtures/content/{skin}.php) and the skin itself.
     */
    protected function seedSubject(string $skin): bool
    {
        $seeded = (new SkinContent($skin))->setContainer($this->app)->__invoke() !== false;
        PublicUrl::forgetPaths();

        return $seeded;
    }
}
