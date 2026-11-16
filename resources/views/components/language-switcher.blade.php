{{--
    Links to this page in the site's other languages.

    <x-atrium-theme::language-switcher :urls="['en' => $enUrl, 'de' => $deUrl]" />

    Give every language of the site: where the page has no version, link to
    that language's home page. Hidden on a site with one language.
--}}
@props(['urls' => []])
@php($languages = app(\Atrium\Admin\Support\ContentLanguages::class))
@if (count($urls) > 1)
<nav class="languages" aria-label="{{ __('atrium-theme::theme.language') }}">
    <ul>
        @foreach ($urls as $code => $url)
            @php($language = $languages->find((string) $code))
            @if ($language)
                <li><a href="{{ $url }}" hreflang="{{ $language->hreflang() }}" lang="{{ $language->hreflang() }}" @if ($code === app()->getLocale()) aria-current="true" @endif>{{ $language->name }}</a></li>
            @endif
        @endforeach
    </ul>
</nav>
@endif
