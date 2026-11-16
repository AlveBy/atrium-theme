{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{--
    The frame of an error page: the code, the title and what the page puts
    inside. A 4xx page is drawn in the theme's layout, with the menu. A 5xx
    page, or one whose layout fails (the database is down), gets a plain
    frame that asks nothing of the database.
--}}
@props(['code', 'title'])
@php
    $error = [
        'errorCode' => (int) $code,
        'errorTitle' => (string) $title,
        'errorBody' => (string) $slot,
        'siteName' => $siteName ?? (string) config('app.name'),
        'homeUrl' => $homeUrl ?? url('/'),
    ];
    $html = null;

    if ($error['errorCode'] < 500) {
        try {
            $html = view('atrium-theme::partials.error-themed', $error)->render();
        } catch (\Throwable $e) {
            report($e);
        }
    }
@endphp
{!! $html ?? view('atrium-theme::partials.error-plain', $error)->render() !!}
