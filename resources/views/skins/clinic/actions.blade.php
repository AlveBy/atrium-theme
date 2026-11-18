{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
@php($home ??= app(\Atrium\Theme\Skins\HomeContent::class))
@if ($phone = $home->phone())
    <span class="sk-contact">
        <a class="sk-phone" href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ $phone }}</a>
        @if ($hours = $home->hours())
            <span>{{ $hours }}</span>
        @endif
    </span>
@endif
@if ($home->hasForm('appointment'))
    <a class="sk-btn sk-btn--small" href="{{ $homeUrl }}#appointment">{{ $skin->text('book') }}</a>
@endif
