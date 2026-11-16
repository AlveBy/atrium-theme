{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{--
    The page frame of the theme. Publish the views (vendor:publish
    --tag=atrium-theme-views) to change it; colours are custom properties
    in the stylesheet below, override them in a stylesheet of your own.
--}}
@php
    $suffix = (string) config('atrium.seo.title_suffix', '');
    $fullTitle = isset($metaTitle) && $metaTitle !== '' ? $metaTitle.$suffix : $siteName;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="generator" content="Atrium">
    <title>{{ $fullTitle }}</title>
    @if (! empty($metaDescription))
    <meta name="description" content="{{ $metaDescription }}">
    @endif
    @if (! empty($metaKeywords))
    <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
    @if (! empty($canonical))
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:url" content="{{ $canonical }}">
    @endif
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:title" content="{{ $metaTitle ?? $siteName }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    @if (! empty($metaDescription))
    <meta property="og:description" content="{{ $metaDescription }}">
    @endif
    @if (! empty($ogImage))
    <meta property="og:image" content="{{ $ogImage }}">
    @endif
    <x-atrium::hreflang :urls="$alternates ?? []" />
    <script>try{var t=localStorage.getItem('atrium-theme');if(t==='dark'||t==='light')document.documentElement.dataset.theme=t}catch(e){}</script>
    <style>{!! $themeStyles !!}</style>
    <x-atrium::head />
    @stack('head')
</head>
<body>
    <x-atrium::body-start />
    <a class="skip" href="#content">{{ __('atrium-theme::theme.skip') }}</a>

    <header class="site-header">
        <div class="wrap">
            <a class="site-name" href="{{ $homeUrl }}">{{ $siteName }}</a>

            @if ($menu !== [])
                <nav class="menu" aria-label="{{ __('atrium-theme::theme.menu') }}">
                    <ul>
                        @foreach ($menu as $item)
                            <li><a href="{{ $item['url'] }}" @if (request()->route('prefix') === $item['prefix']) aria-current="page" @endif>{{ $item['label'] }}</a></li>
                        @endforeach
                    </ul>
                </nav>
            @endif

            <x-atrium-theme::language-switcher :urls="$switch ?? []" />
            <x-atrium-theme::theme-toggle />
        </div>
    </header>

    <main id="content" class="wrap">
        @if (! empty($breadcrumbs))
            <nav class="breadcrumbs" aria-label="{{ __('atrium-theme::theme.breadcrumbs') }}">
                <ol>
                    <li><a href="{{ $homeUrl }}">{{ $siteName }}</a></li>
                    @foreach ($breadcrumbs as $crumb)
                        <li><a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a></li>
                    @endforeach
                </ol>
            </nav>
        @endif

        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="wrap">
            <span>© {{ now()->year }} {{ $siteName }}</span>
            @if (config('atrium-theme.credit', true))
                <a class="credit" href="https://github.com/AlveBy/atrium">{{ __('atrium-theme::theme.credit') }}</a>
            @endif
        </div>
    </footer>

    <x-atrium::body-end />
</body>
</html>
