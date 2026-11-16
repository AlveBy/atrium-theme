@extends('atrium-theme::layout')

@section('content')
    <h1>{{ $siteName }}</h1>

    @forelse ($modules as $module)
        <section class="module">
            <div class="module-head">
                <h2>{{ $module['label'] }}</h2>
                <a href="{{ $module['url'] }}">{{ __('atrium-theme::theme.all') }}</a>
            </div>

            @if ($module['records'] !== [])
                @include('atrium-theme::partials.cards', ['records' => $module['records'], 'heading' => 'h3'])
            @else
                <p class="empty">{{ __('atrium-theme::theme.empty') }}</p>
            @endif
        </section>
    @empty
        <p class="empty">{{ __('atrium-theme::theme.empty') }}</p>
    @endforelse
@endsection
