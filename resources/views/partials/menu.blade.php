{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{--
    The menu the theme builds by itself: a link to each module with pages.
    The menus module draws its own menu instead, and falls back to this one
    while the site has no menu with the header code.
--}}
@if ($menu !== [])
    <nav class="menu" aria-label="{{ __('atrium-theme::theme.menu') }}">
        <ul>
            @foreach ($menu as $item)
                <li><a href="{{ $item['url'] }}" @if (request()->route('prefix') === $item['prefix']) aria-current="page" @endif>{{ $item['label'] }}</a></li>
            @endforeach
        </ul>
    </nav>
@endif
