{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{--
    Publish the error pages to make them your own:
    php artisan vendor:publish --tag=atrium-errors
    copies them to resources/views/errors, which wins over the theme.
--}}
<x-atrium-theme::error-page :code="404" :title="__('atrium-theme::errors.404.title')">
    <p>{{ __('atrium-theme::errors.404.message') }}</p>

    @if (! empty($searchUrl))
        <form class="error-search" role="search" method="get" action="{{ $searchUrl }}">
            <label for="error-search">{{ __('atrium-theme::errors.search') }}</label>
            <input id="error-search" type="search" name="q" maxlength="200">
            <button type="submit">{{ __('atrium-theme::errors.search_button') }}</button>
        </form>
    @endif

    <p><a href="{{ $homeUrl ?? url('/') }}">{{ __('atrium-theme::errors.home') }}</a></p>
</x-atrium-theme::error-page>
