{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{--
    Publish the error pages to make them your own:
    php artisan vendor:publish --tag=atrium-errors
    copies them to resources/views/errors, which wins over the theme.
--}}
<x-atrium-theme::error-page :code="503" :title="__('atrium-theme::errors.503.title')">
    <p>{{ __('atrium-theme::errors.503.message') }}</p>
    <p><a href="{{ $homeUrl ?? url('/') }}">{{ __('atrium-theme::errors.home') }}</a></p>
</x-atrium-theme::error-page>
