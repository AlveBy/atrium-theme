{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
@php($home ??= app(\Atrium\Theme\Skins\HomeContent::class))
@if ($home->hasForm('table-booking'))
    <a class="sk-btn sk-btn--small" href="{{ $homeUrl }}#booking">{{ $skin->text('book') }}</a>
@endif
