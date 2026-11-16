{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{--
    Publish the error pages to make them your own:
    php artisan vendor:publish --tag=atrium-errors
    copies them to resources/views/errors, which wins over the theme.
--}}
<x-atrium-theme::error-page :code="isset($exception) ? $exception->getStatusCode() : 500" :title="__('atrium-theme::errors.5xx.title')">
    <p>{{ __('atrium-theme::errors.5xx.message') }}</p>
    <p><a href="{{ $homeUrl ?? url('/') }}">{{ __('atrium-theme::errors.home') }}</a></p>
</x-atrium-theme::error-page>
