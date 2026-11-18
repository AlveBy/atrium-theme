{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{--
    Medical centre. The hero is the page "home" (Pages) with the
    appointment form, the services are the catalog, the doctors are the
    pages under "doctors", then news and questions.
--}}
@extends('atrium-theme::layout')

@section('main-class', 'sk-home')

@section('content')
    @php
        $hero = $home->page('home');
        $services = $home->products(6);
        $doctorsPage = $home->page('doctors');
        $doctors = $home->children('doctors')->take(3);
        $news = $home->news(3);
        $questions = $home->questions(3);
    @endphp

    <section class="sk-hero wrap" aria-labelledby="sk-hero-title">
        <div class="sk-hero__text">
            <h1 id="sk-hero-title">{{ $hero?->name ?: $siteName }}</h1>
            @if ($hero && filled($hero->content))
                <div class="sk-lead">{!! $hero->content !!}</div>
            @endif
            @if ($home->hasForm('appointment'))
                <div id="appointment">
                    @include('atrium-theme::skins.partials.form', ['code' => 'appointment', 'class' => 'sk-form--panel'])
                </div>
            @endif
        </div>
        <div class="sk-hero__art">@include('atrium-theme::skins.clinic.pulse')</div>
    </section>

    @if ($services !== [])
        <section class="sk-band sk-band--tight" aria-labelledby="sk-services-title">
            <div class="wrap">
                @include('atrium-theme::skins.partials.head', ['id' => 'sk-services-title', 'title' => $skin->text('services_title'), 'url' => $home->indexUrl($home::PRODUCT), 'all' => $skin->text('services_all')])
                <ul class="sk-services" role="list">
                    @foreach ($services as $service)
                        <li class="sk-service">
                            @if ($image = $service->thumbnail())
                                <img src="{{ $image }}" alt="" width="56" height="56" loading="lazy">
                            @endif
                            <h3><a href="{{ $service->url() }}">{{ $service->title() }}</a></h3>
                            @if ($teaser = $service->teaser())
                                <div class="sk-service__text">{!! $teaser !!}</div>
                            @endif
                            @if ($price = $home->price($service, $skin))
                                <p class="sk-price">{{ $price }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($doctors->isNotEmpty())
        <section class="sk-band sk-band--soft" aria-labelledby="sk-doctors-title">
            <div class="wrap">
                @include('atrium-theme::skins.partials.head', ['id' => 'sk-doctors-title', 'title' => $doctorsPage->name, 'url' => $home->pageUrl($doctorsPage), 'all' => $skin->text('doctors_all')])
                <ul class="sk-doctors" role="list">
                    @foreach ($doctors as $doctor)
                        @php
                            $initials = collect(preg_split('/\s+/u', trim((string) $doctor->name)) ?: [])->take(2)->map(fn ($word) => mb_substr($word, 0, 1))->implode('');
                            preg_match('#<p[^>]*>(.*?)</p>#s', (string) $doctor->content, $role);
                        @endphp
                        <li class="sk-doctor">
                            <span class="sk-doctor__photo" aria-hidden="true">{{ $initials }}</span>
                            <span class="sk-doctor__text">
                                @if ($url = $home->pageUrl($doctor))
                                    <strong><a class="sk-doctor__name" href="{{ $url }}">{{ $doctor->name }}</a></strong>
                                @else
                                    <strong>{{ $doctor->name }}</strong>
                                @endif
                                @if (! empty($role[1]))
                                    <span class="sk-muted">{{ strip_tags($role[1]) }}</span>
                                @endif
                                @if ($home->hasForm('appointment'))
                                    <a href="#appointment">{{ $skin->text('book') }}<span class="sk-visually-hidden">: {{ $doctor->name }}</span></a>
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($news !== [])
        <section class="sk-band" aria-labelledby="sk-news-title">
            <div class="wrap">
                @include('atrium-theme::skins.partials.head', ['id' => 'sk-news-title', 'title' => $skin->text('news_title'), 'url' => $home->indexUrl($home::NEWS), 'all' => __('atrium-theme::theme.all')])
                <ul class="sk-news" role="list">
                    @foreach ($news as $item)
                        <li class="sk-news__item">
                            @if ($date = $item->date())
                                <time datetime="{{ $date->toAtomString() }}">{{ $date->isoFormat('LL') }}</time>
                            @endif
                            <a href="{{ $item->url() }}">{{ $item->title() }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($questions->isNotEmpty())
        @include('atrium-theme::skins.partials.faq', ['title' => $skin->text('faq_title')])
    @endif
@endsection
