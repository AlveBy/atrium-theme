{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
@php($home ??= app(\Atrium\Theme\Skins\HomeContent::class))
@if ($phone = $home->phone())
    <a class="sk-phone" href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ $phone }}</a>
@endif
