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
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"@if ($skin) data-skin="{{ $skin->name }}"@if ($skin->scheme === 'dark') data-theme="dark"@endif @endif>
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
    @if ($skin)
    <style>{!! $skin->styles() !!}</style>
    @endif
    <x-atrium::head />
    @stack('head')
</head>
<body>
    <x-atrium::body-start />
    <a class="skip" href="#content">{{ __('atrium-theme::theme.skip') }}</a>

    @if ($skin)
        @includeIf($skin->view('banner'))
    @endif
    <header class="site-header">
        <div class="wrap">
            @if ($skin)
            <a class="site-name" href="{{ $homeUrl }}">@include($skin->view('brand'))</a>
            @else
            <a class="site-name" href="{{ $homeUrl }}">{{ $siteName }}</a>
            @endif

            @include((string) (config('atrium-theme.header_menu') ?: 'atrium-theme::partials.menu'))

            @foreach ((array) config('atrium-theme.header_views', []) as $headerView)
                @include($headerView)
            @endforeach

            <x-atrium-theme::language-switcher :urls="$switch ?? []" />
            <x-atrium-theme::theme-toggle />
            @if ($skin)
                @includeIf($skin->view('actions'))
            @endif
        </div>
    </header>

    <main id="content" class="@yield('main-class', 'wrap')">
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
        @if ($skin)
            @include($skin->view('footer'))
        @else
        <div class="wrap">
            @if (config('atrium-theme.footer_menu'))
                @include((string) config('atrium-theme.footer_menu'))
            @endif
            <span>© {{ now()->year }} {{ $siteName }}</span>
            @if (config('atrium-theme.credit', true))
                <a class="credit" href="https://github.com/AlveBy/atrium">{{ __('atrium-theme::theme.credit') }}</a>
            @endif
        </div>
        @endif
    </footer>

    <x-atrium::body-end />
</body>
</html>
