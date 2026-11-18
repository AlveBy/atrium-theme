{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{-- The newest news item as a strip above the header. --}}
@php($latest = app(\Atrium\Theme\Skins\HomeContent::class)->news(1)[0] ?? null)
@if ($latest)
    <p class="sk-banner"><a href="{{ $latest->url() }}">{{ $latest->title() }}</a></p>
@endif
