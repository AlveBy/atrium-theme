<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme;

use Atrium\Admin\Contracts\Sitemapable;
use Atrium\Admin\Support\ContentLanguages;
use Atrium\Admin\Support\PublicUrl;
use Atrium\Admin\Support\Sitemap;
use Atrium\Admin\Support\Sites;
use Atrium\Theme\Errors\ErrorPages;
use Atrium\Theme\Errors\ErrorViewFinder;
use Atrium\Theme\Http\Controllers\PageController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Factory;

final class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/atrium-theme.php', 'atrium-theme');
        $this->app->singleton(Pages::class);

        // The theme's error pages, between the app's own and Laravel's.
        $this->app->extend('view', function (Factory $view): Factory {
            ErrorViewFinder::install($view, new ErrorPages);

            return $view;
        });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'atrium-theme');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'atrium-theme');

        View::composer(['atrium-theme::layout', 'atrium-theme::home', 'atrium-theme::list', 'atrium-theme::page'], function (\Illuminate\View\View $view): void {
            $pages = $this->app->make(Pages::class);
            $menu = [];

            foreach ($pages->prefixes() as $prefix) {
                $class = $pages->elementModels($prefix)[0] ?? $pages->sectionModels($prefix)[0];
                $menu[] = ['label' => $pages->label($class), 'url' => $pages->indexUrl($prefix), 'prefix' => $prefix];
            }

            $view->with([
                'menu' => $menu,
                'siteName' => (string) ($this->app->make(Sites::class)->current()->name ?: config('app.name')),
                'homeUrl' => $pages->homeUrl(),
                'themeStyles' => self::styles(),
            ]);
        });

        // Every error page, the app's own included, knows the site and its
        // language, even for an address that matched no route.
        View::composer(['errors::*', 'atrium-theme::components.error-page'], function (\Illuminate\View\View $view): void {
            $pages = new ErrorPages;

            if ($pages->themed()) {
                $view->with($pages->prepare());
            }
        });

        // After every provider and the app's own routes: a route the app
        // defines itself is matched first, and an app's own "/" stays.
        $this->app->booted(fn () => $this->registerRoutes());

        $this->callAfterResolving(Sitemap::class, function (Sitemap $sitemap): void {
            $sitemap->extend(
                fn (): iterable => $this->sitemapRecords(),
                fn (Model $record, string $language): ?string => $this->sitemapUrl($record, $language),
            );
        });

        $flush = function (string $event, array $payload): void {
            $record = $payload[0] ?? null;

            if ($record instanceof Model && ! $record instanceof Sitemapable && PublicUrl::ruleFor($record::class) !== null) {
                $this->app->make(Sitemap::class)->flush();
                PublicUrl::forgetPaths();
            }
        };

        Event::listen('eloquent.saved: *', $flush);
        Event::listen('eloquent.deleted: *', $flush);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/atrium-theme.php' => config_path('atrium-theme.php'),
            ], 'atrium-theme-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/atrium-theme'),
            ], 'atrium-theme-views');

            // The error pages as a start for the app's own, Laravel's way:
            // resources/views/errors wins over the theme and stays the app's.
            $this->publishes([
                __DIR__.'/../resources/views/errors' => resource_path('views/errors'),
            ], 'atrium-errors');
        }
    }

    /**
     * The stylesheet goes inline: one request less, and nothing to publish.
     */
    public static function styles(): string
    {
        static $styles = null;

        return $styles ??= (string) file_get_contents(__DIR__.'/../resources/css/theme.css');
    }

    private function registerRoutes(): void
    {
        if (! config('atrium-theme.routes', true) || $this->app->routesAreCached()) {
            return;
        }

        $prefixes = $this->app->make(Pages::class)->prefixes();
        $hasHome = collect(Route::getRoutes()->getRoutes())
            ->contains(fn (RoutingRoute $route): bool => $route->uri() === '/' && in_array('GET', $route->methods(), true));

        Route::middleware('web')->group(function () use ($prefixes, $hasHome): void {
            Route::localized(function () use ($prefixes, $hasHome): void {
                if (! $hasHome) {
                    Route::get('/', [PageController::class, 'home'])->name('atrium.theme.home');
                }

                if ($prefixes === []) {
                    return;
                }

                $pattern = implode('|', array_map(static fn (string $prefix): string => preg_quote($prefix, '#'), $prefixes));

                Route::get('{prefix}', [PageController::class, 'index'])
                    ->where('prefix', $pattern)
                    ->name('atrium.theme.index');

                Route::get('{prefix}/{path}', [PageController::class, 'show'])
                    ->where(['prefix' => $pattern, 'path' => '.+'])
                    ->name('atrium.theme.page');
            });
        });
    }

    /**
     * Records of the modules with pages that do not list themselves in the
     * sitemap (Sitemapable modules do).
     *
     * @return iterable<Model>
     */
    private function sitemapRecords(): iterable
    {
        $pages = $this->app->make(Pages::class);

        foreach ($pages->modules() as $module) {
            foreach (array_keys($module) as $class) {
                if (! is_subclass_of($class, Sitemapable::class)) {
                    yield from $pages->visible($class)->lazy(500);
                }
            }
        }
    }

    /**
     * A record that is not translated has one page, in the default language.
     */
    private function sitemapUrl(Model $record, string $language): ?string
    {
        $pages = $this->app->make(Pages::class);

        if (! $pages->isTranslatable($record, 'code') && $language !== $this->app->make(ContentLanguages::class)->default()) {
            return null;
        }

        return $pages->url($record, $language);
    }
}
