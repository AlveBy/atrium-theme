{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{-- An error page in the theme's layout. --}}
@extends('atrium-theme::layout', ['metaTitle' => $errorTitle])

@section('content')
    @include('atrium-theme::partials.error-body')
@endsection
