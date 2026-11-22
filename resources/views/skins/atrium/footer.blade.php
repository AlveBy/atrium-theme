{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
@php
    $home ??= app(\Atrium\Theme\Skins\HomeContent::class);
    $sections = $home->sections();
    $store = $home->stores()[0] ?? null;
    $email = $store?->record->getAttribute('email');
    $phone = $home->phone();
@endphp
<div class="wrap at-footer">
    <div class="at-footer__brand">
        <a class="at-footer__name" href="{{ $homeUrl }}">@include($skin->view('brand'))</a>
        <p>{{ $skin->text('tagline') }}</p>
        @if (config('atrium-theme.credit', true))
            <a class="credit" href="https://github.com/AlveBy/atrium">{{ __('atrium-theme::theme.credit') }}</a>
        @endif
    </div>
    @if ($sections !== [])
        <nav aria-labelledby="at-footer-shop">
            <h2 id="at-footer-shop">{{ $skin->text('modules.catalog') }}</h2>
            <ul>
                @foreach ($sections as $section)
                    <li><a href="{{ $section->url() }}">{{ $section->title() }}</a></li>
                @endforeach
            </ul>
        </nav>
    @endif
    @if (config('atrium-theme.footer_menu'))
        <nav aria-labelledby="at-footer-menu">
            <h2 id="at-footer-menu">{{ $skin->text('footer_menu') }}</h2>
            @include((string) config('atrium-theme.footer_menu'))
        </nav>
    @endif
    @if ($store)
        <div>
            <h2>{{ $skin->text('contact_footer') }}</h2>
            <ul>
                @if ($phone)
                    <li><a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ $phone }}</a></li>
                @endif
                @if (filled($email))
                    <li><a href="mailto:{{ $email }}">{{ $email }}</a></li>
                @endif
                <li>{{ $home->address($store) }}</li>
            </ul>
        </div>
    @endif
</div>
<div class="wrap at-footer__base">
    <span>© {{ now()->year }} {{ $siteName }}</span>
    <span>{{ $skin->text('footer_note') }}</span>
</div>
