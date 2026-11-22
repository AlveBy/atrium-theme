{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{-- A store: its name, facts and map, and whatever text and photos it has. --}}
@php
    $blocks = collect($page->blocks())->reject(fn (array $block): bool => $block['kind'] === 'properties')->values()->all();
    $text = $page->body();
    $image = $page->image();
@endphp
<article class="at-store-page">
    <div class="at-store-page__info">
        <h1>{{ $page->title() }}</h1>
        <ul role="list" class="at-stores at-stores--single">
            @include('atrium-theme::skins.atrium.store-card', ['store' => $page, 'map' => true, 'single' => true])
        </ul>
        @if ($image)
            <div class="at-store-page__photo"><img src="{{ \Atrium\Admin\Support\Cdn::image($image, 1280) }}" alt="{{ $page->title() }}"></div>
        @endif
    </div>
    <div class="at-store-page__more">
        @if ($text)
            <div class="at-prose">{!! $text !!}</div>
        @endif
        @include('atrium-theme::skins.atrium.blocks', ['blocks' => $blocks])
    </div>
</article>
