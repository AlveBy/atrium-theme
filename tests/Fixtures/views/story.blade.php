{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
@extends('atrium-theme::layout', ['metaTitle' => $page->title()])

@push('head')
    <meta name="story" content="{{ $page->title() }}">
@endpush

@section('content')
    <h1 class="story">{{ $page->title() }}</h1>
@endsection
