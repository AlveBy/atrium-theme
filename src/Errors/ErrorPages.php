<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Errors;

use Atrium\Admin\Http\Middleware\EnterSiteFolder;
use Atrium\Admin\Models\Site;
use Atrium\Admin\Support\ContentLanguages;
use Atrium\Admin\Support\Sites;
use Atrium\Theme\Pages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Throwable;

/**
 * What an error page needs to know about the request it answers.
 *
 * An address that matches no route never passes the site and language
 * middleware, so the page finds them itself: the site by domain and folder,
 * the language by the prefix of the address. Nothing here may break the
 * page: with the database down it falls back to the app's name and "/".
 */
final class ErrorPages
{
    /**
     * The admin keeps its own screens: the theme's pages are for visitors.
     */
    public function themed(): bool
    {
        $prefix = trim((string) config('atrium.prefix', 'admin'), '/');

        return ! $this->request()->is($prefix, $prefix.'/*');
    }

    /**
     * The code of the site the request is on, for resources/views/errors/{code}.
     */
    public function siteCode(): string
    {
        return (string) $this->site()->code;
    }

    /**
     * Picks the site and the language for the page, then hands it the site's
     * name and the addresses of its home and search pages.
     *
     * @return array{siteName:string,homeUrl:string,searchUrl:?string}
     */
    public function prepare(): array
    {
        $data = ['siteName' => (string) config('app.name'), 'homeUrl' => url('/'), 'searchUrl' => null];

        try {
            $sites = app(Sites::class);
            $sites->setCurrent($this->site());

            $languages = app(ContentLanguages::class);
            $prefix = (string) $this->request()->segment(1);
            app()->setLocale($prefix !== $languages->default() && $languages->has($prefix) ? $prefix : $languages->default());

            $data['siteName'] = $sites->current()->name ?: $data['siteName'];
            $data['homeUrl'] = app(Pages::class)->homeUrl();
            $data['searchUrl'] = Route::has('atrium.search') ? $languages->route('atrium.search') : null;
        } catch (Throwable) {
            // The page is drawn with what is known so far.
        }

        return $data;
    }

    /**
     * A visitor's site comes from the address, as ResolveSite picks it: the
     * request may never have reached that middleware, and a site set for an
     * earlier request (Octane) is not this one's. The admin's is the one
     * picked in its header.
     */
    private function site(): Site
    {
        $sites = app(Sites::class);

        if (! $this->themed()) {
            return $sites->current();
        }

        return $sites->find($this->request()->attributes->get(EnterSiteFolder::SITE))
            ?? $sites->forHost($this->request()->getHost());
    }

    private function request(): Request
    {
        return app('request');
    }
}
