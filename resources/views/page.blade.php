{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
@extends('atrium-theme::layout', [
    'metaTitle' => $page->seoTitle(),
    'metaDescription' => $page->seoDescription(),
    'metaKeywords' => $page->seoKeywords(),
    'ogImage' => $page->ogImage(),
    'ogType' => 'article',
])

@section('content')
    <article>
        <h1>{{ $page->title() }}</h1>

        @if ($date = $page->date())
            <p class="date"><time datetime="{{ $date->toAtomString() }}">{{ $date->isoFormat('LL') }}</time></p>
        @endif

        @if ($image = $page->image())
            <img class="lead-image" src="{{ \Atrium\Admin\Support\Cdn::image($image, 1280) }}" @if ($srcset = \Atrium\Admin\Support\Cdn::srcset($image)) srcset="{{ $srcset }}" sizes="(min-width: 72rem) 70rem, 100vw" @endif alt="{{ $page->title() }}">
        @endif

        @if ($body = $page->body())
            <div class="content">{!! $body !!}</div>
        @endif

        @foreach ($page->blocks() as $block)
            @include('atrium-theme::partials.block', ['block' => $block])
        @endforeach
    </article>

    {{-- Other packages add their part under a record: reviews, comments, a form. --}}
    @foreach ((array) config('atrium-theme.record_views', []) as $recordView)
        @include($recordView, ['record' => $page->record, 'page' => $page])
    @endforeach
@endsection
