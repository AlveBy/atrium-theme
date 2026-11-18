{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{--
    Travel agency. The hero is the page with the code "home" (Pages), the
    destinations are the catalog, the advantages are the pages under
    "why-us", then reviews, questions and the call-back form.
--}}
@extends('atrium-theme::layout')

@section('main-class', 'sk-home')

@section('content')
    @php
        $hero = $home->page('home');
        $tours = $home->products(4);
        $features = $home->children('why-us');
        $reviews = $home->reviews(3);
        $questions = $home->questions(4);
    @endphp

    <section class="sk-hero wrap" aria-labelledby="sk-hero-title">
        <div class="sk-hero__text">
            <p class="sk-eyebrow">{{ $skin->text('eyebrow') }}</p>
            <h1 id="sk-hero-title">{{ $hero?->name ?: $siteName }}</h1>
            @if ($hero && filled($hero->content))
                <div class="sk-lead">{!! $hero->content !!}</div>
            @endif
            @if ($home->hasForm('tour-request'))
                @include('atrium-theme::skins.partials.form', ['code' => 'tour-request', 'class' => 'sk-form--panel'])
            @endif
        </div>
        <div class="sk-hero__art sk-hero__art--round">{{ $skin->illustration('travel-coast') }}</div>
    </section>

    @if ($tours !== [])
        <section class="sk-band sk-band--soft" aria-labelledby="sk-tours-title">
            <div class="wrap">
                @include('atrium-theme::skins.partials.head', ['id' => 'sk-tours-title', 'title' => $skin->text('catalog_title'), 'url' => $home->indexUrl($home::PRODUCT), 'all' => $skin->text('catalog_all')])
                <ul class="sk-tours" role="list">
                    @foreach ($tours as $tour)
                        <li class="sk-tour">
                            @if ($image = $tour->thumbnail())
                                <img src="{{ $image }}" alt="" loading="lazy">
                            @endif
                            <div class="sk-tour__body">
                                <h3><a href="{{ $tour->url() }}">{{ $tour->title() }}</a></h3>
                                @if ($teaser = $tour->teaser())
                                    <div class="sk-tour__text">{!! $teaser !!}</div>
                                @endif
                                @if ($price = $home->price($tour, $skin))
                                    <p class="sk-price">{{ $price }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($features->isNotEmpty())
        <section class="sk-band" aria-labelledby="sk-features-title">
            <div class="wrap">
                <h2 id="sk-features-title" class="sk-visually-hidden">{{ $home->page('why-us')->name }}</h2>
                <ul class="sk-features" role="list">
                    @foreach ($features as $feature)
                        <li class="sk-feature">
                            <span class="sk-feature__icon">@include('atrium-theme::skins.travel.icon', ['index' => $loop->index])</span>
                            <h3>{{ $feature->name }}</h3>
                            <div class="sk-feature__text">{!! $feature->content !!}</div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($reviews->isNotEmpty())
        <section class="sk-band sk-band--dark" aria-labelledby="sk-reviews-title">
            <div class="wrap">
                <h2 id="sk-reviews-title">{{ $skin->text('reviews_title') }}</h2>
                @include('atrium-theme::skins.partials.reviews')
            </div>
        </section>
    @endif

    @if ($questions->isNotEmpty())
        @include('atrium-theme::skins.partials.faq', ['title' => $skin->text('faq_title')])
    @endif

    @if ($home->hasForm('callback'))
        <section class="sk-band sk-band--sun" aria-labelledby="sk-callback-title">
            <div class="wrap sk-callback">
                <div class="sk-callback__text">
                    <h2 id="sk-callback-title">{{ $skin->text('callback_title') }}</h2>
                    <p>{{ $skin->text('callback_text') }}</p>
                </div>
                @include('atrium-theme::skins.partials.form', ['code' => 'callback', 'class' => 'sk-form--inline'])
            </div>
        </section>
    @endif
@endsection
