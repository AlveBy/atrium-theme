{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{--
    One record: a product (gallery, price, details, related products), a
    store (facts and map) or anything else as an article. Other packages
    add their part under it through `atrium-theme.record_views`.
--}}
@extends('atrium-theme::layout', [
    'metaTitle' => $page->seoTitle(),
    'metaDescription' => $page->seoDescription(),
    'metaKeywords' => $page->seoKeywords(),
    'ogImage' => $page->ogImage(),
    'ogType' => 'article',
])

@php
    $home = app(\Atrium\Theme\Skins\HomeContent::class);
    $kind = match (true) {
        $page->record instanceof ($home::PRODUCT) => 'product',
        $page->record instanceof ($home::STORE) => 'store',
        default => 'article',
    };
@endphp

@section('content')
    @include('atrium-theme::skins.atrium.page-'.$kind)

    @foreach ((array) config('atrium-theme.record_views', []) as $recordView)
        @include($recordView, ['record' => $page->record, 'page' => $page])
    @endforeach
@endsection
