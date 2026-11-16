{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{--
    An error page without the layout: the theme's stylesheet, the site's name
    and a way home. Nothing here reads the database, so it still draws when
    the error is the database.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="generator" content="Atrium">
    <title>{{ $errorTitle.config('atrium.seo.title_suffix', '') }}</title>
    <script>try{var t=localStorage.getItem('atrium-theme');if(t==='dark'||t==='light')document.documentElement.dataset.theme=t}catch(e){}</script>
    <style>{!! \Atrium\Theme\ThemeServiceProvider::styles() !!}</style>
</head>
<body>
    <header class="site-header">
        <div class="wrap">
            <a class="site-name" href="{{ $homeUrl }}">{{ $siteName }}</a>
        </div>
    </header>

    <main id="content" class="wrap">
        @include('atrium-theme::partials.error-body')
    </main>

    <footer class="site-footer">
        <div class="wrap">
            <span>© {{ now()->year }} {{ $siteName }}</span>
            @if (config('atrium-theme.credit', true))
                <a class="credit" href="https://github.com/AlveBy/atrium">{{ __('atrium-theme::theme.credit') }}</a>
            @endif
        </div>
    </footer>
</body>
</html>
