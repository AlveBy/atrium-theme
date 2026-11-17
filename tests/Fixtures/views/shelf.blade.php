{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
@extends('atrium-theme::layout', ['metaTitle' => $page->title()])

@section('content')
    <h1 class="shelf">{{ $page->title() }}</h1>
@endsection
