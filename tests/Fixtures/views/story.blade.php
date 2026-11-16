@extends('atrium-theme::layout', ['metaTitle' => $page->title()])

@push('head')
    <meta name="story" content="{{ $page->title() }}">
@endpush

@section('content')
    <h1 class="story">{{ $page->title() }}</h1>
@endsection
