{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{-- Records as cards: picture, date, title, short text. --}}
<ul class="cards">
    @foreach ($records as $record)
        @continue(! ($url = $record->url()))
        <li class="card">
            @if ($image = $record->thumbnail())
                <img src="{{ \Atrium\Admin\Support\Cdn::image($image, 640) }}" @if ($srcset = \Atrium\Admin\Support\Cdn::srcset($image, 1280)) srcset="{{ $srcset }}" sizes="(min-width: 72rem) 22rem, (min-width: 36rem) 50vw, 100vw" @endif alt="" loading="lazy">
            @endif
            <div class="card-body">
                @if ($date = $record->date())
                    <p class="date"><time datetime="{{ $date->toAtomString() }}">{{ $date->isoFormat('LL') }}</time></p>
                @endif
                <{{ $heading }}><a href="{{ $url }}">{{ $record->title() }}</a></{{ $heading }}>
                @if ($teaser = $record->teaser())
                    <div class="card-text">@embeds($teaser)</div>
                @endif
            </div>
        </li>
    @endforeach
</ul>
