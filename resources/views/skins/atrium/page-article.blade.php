{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{-- Anything else with a page of its own: news, a plain page, a member of the team. --}}
@php
    $image = $page->image();
    $text = $page->body();
@endphp
<article class="at-article">
    @if ($image)
        <figure class="at-article__image"><img src="{{ \Atrium\Admin\Support\Cdn::image($image, 1280) }}" @if ($srcset = \Atrium\Admin\Support\Cdn::srcset($image)) srcset="{{ $srcset }}" sizes="(min-width: 72rem) 52rem, 100vw" @endif alt="{{ $page->title() }}" fetchpriority="high"></figure>
    @endif
    @if ($date = $page->date())
        <p class="at-date"><time datetime="{{ $date->toAtomString() }}">{{ $date->isoFormat('LL') }}</time></p>
    @endif
    <h1>{{ $page->title() }}</h1>
    @if ($text)
        <div class="at-prose">{!! $text !!}</div>
    @endif
    @include('atrium-theme::skins.atrium.blocks', ['blocks' => $page->blocks()])
</article>
