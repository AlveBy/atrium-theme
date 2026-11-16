@extends('atrium-theme::layout', [
    'metaTitle' => isset($page) ? $page->seoTitle() : $title,
    'metaDescription' => isset($page) ? $page->seoDescription() : null,
    'metaKeywords' => isset($page) ? $page->seoKeywords() : null,
    'ogImage' => isset($page) ? $page->ogImage() : null,
])

@section('content')
    <h1>{{ $title }}</h1>

    @if ($text)
        <div class="content">{!! $text !!}</div>
    @endif

    @if ($sections !== [])
        <ul class="sections">
            @foreach ($sections as $section)
                <li><a href="{{ $section->url() }}">{{ $section->title() }}</a></li>
            @endforeach
        </ul>
    @endif

    @if ($records !== null && $records->isNotEmpty())
        @include('atrium-theme::partials.cards', ['records' => $records, 'heading' => 'h2'])
        @include('atrium-theme::partials.pagination', ['paginator' => $records])
    @elseif ($sections === [])
        <p class="empty">{{ __('atrium-theme::theme.empty') }}</p>
    @endif
@endsection
