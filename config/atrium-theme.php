<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Pages of the site
    |--------------------------------------------------------------------------
    |
    | The theme draws a page for every module listed in
    | config('atrium.public_urls'): the home page, each module's list, its
    | sections and records. Set to false to keep only your own routes.
    |
    | A route of your own always wins: the theme's are registered after the
    | app's, and the home page is left alone when the app has its own "/".
    |
    */
    'routes' => (bool) env('ATRIUM_THEME_ROUTES', true),

    /*
    |--------------------------------------------------------------------------
    | Skin
    |--------------------------------------------------------------------------
    |
    | A ready-made look for a kind of site: travel, restaurant, clinic or
    | shop. Colours, fonts and a home page built from the modules (catalog,
    | news, reviews, FAQ, forms, stores, brands, pages). Empty for the
    | neutral theme.
    |
    | The fonts are files of the theme: `vendor:publish --tag=atrium-theme-assets`
    | (atrium:install does it) puts them in public/vendor/atrium-theme.
    |
    */
    'skin' => env('ATRIUM_THEME_SKIN'),

    // The currency of the prices a skin shows (ISO 4217), written the way
    // the page's language writes money: 1.234 € in German, €1,234 in English.
    'currency' => env('ATRIUM_THEME_CURRENCY', 'EUR'),

    // Records per page in a list.
    'per_page' => 20,

    // The latest records of each module on the home page.
    'home_items' => 3,

    // Views drawn under every record page with $record and $page; a module
    // package (reviews, comments) pushes its own here from its provider.
    'record_views' => [],

    // A small "Made with Atrium" link in the footer. Atrium is free; if it
    // helped you, please keep it. Set ATRIUM_THEME_CREDIT=false to hide it.
    'credit' => (bool) env('ATRIUM_THEME_CREDIT', true),
];
