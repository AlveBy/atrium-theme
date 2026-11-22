{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{-- A record in a list of news or of any other module: picture, date, name, teaser. --}}
@if ($url = $record->url())
<li class="at-card">
    @if ($image = $record->thumbnail())
        <img src="{{ \Atrium\Admin\Support\Cdn::image($image, 640) }}" @if ($srcset = \Atrium\Admin\Support\Cdn::srcset($image, 1280)) srcset="{{ $srcset }}" sizes="(min-width: 72rem) 22rem, (min-width: 36rem) 50vw, 100vw" @endif alt="" loading="lazy">
    @endif
    <div class="at-card__body">
        @if ($date = $record->date())
            <p class="at-date"><time datetime="{{ $date->toAtomString() }}">{{ $date->isoFormat('LL') }}</time></p>
        @endif
        <{{ $heading }}><a href="{{ $url }}">{{ $record->title() }}</a></{{ $heading }}>
        @if ($teaser = $record->teaser())
            <div class="at-teaser">{!! $teaser !!}</div>
        @endif
    </div>
</li>
@endif
