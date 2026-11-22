{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{--
    The demo's home page, built from whatever modules are installed: a hero
    (the page "home" of Pages, or the skin's own words), brands, the
    catalog's sections and newest products, news, stores, the team,
    reviews, questions and a contact form. A block whose module is missing,
    or has nothing to show, is left out.
--}}
@extends('atrium-theme::layout')

@section('main-class', 'sk-home')

@section('content')
    @php
        $hero = $home->page('home');
        $catalog = $home->indexUrl($home::PRODUCT);
        $storesUrl = $home->indexUrl($home::STORE);
        $newsUrl = $home->indexUrl($home::NEWS);
        $teamUrl = $home->indexUrl($home::MEMBER);
        $products = $home->products(6);
        $sections = array_slice($home->sections(), 0, 5);
        $brands = $home->brands();
        $news = $home->news(3);
        $stores = $home->stores();
        $team = $home->team(4);
        $reviews = $home->reviews(3);
        $questions = $home->questions(5);
        $form = collect(['contact', 'order', 'callback'])->first(fn (string $code): bool => $home->hasForm($code));
        $stack = array_slice($products, 0, 3);
    @endphp

    <section class="wrap at-hero @if (count($stack) < 3) at-hero--single @endif" aria-labelledby="at-hero-title">
        <div class="at-hero__text">
            <h1 id="at-hero-title">{{ $hero?->name ?: $skin->text('hero_title') }}</h1>
            <div class="at-hero__lead">
                @if ($hero && filled($hero->content))
                    {!! $hero->content !!}
                @else
                    <p>{{ $skin->text('hero_text') }}</p>
                @endif
            </div>
            <div class="at-hero__actions">
                @if ($catalog)
                    <a class="sk-btn" href="{{ $catalog }}">{{ $skin->text('hero_catalog') }}</a>
                @endif
                @if ($storesUrl && $stores !== [])
                    <a class="sk-btn sk-btn--ghost" href="{{ $storesUrl }}">{{ $skin->text('hero_stores') }}</a>
                @endif
            </div>
        </div>
        @if (count($stack) === 3)
            <div class="at-stack">
                @foreach ($stack as $tile)
                    <a class="at-tile" href="{{ $tile->url() }}">
                        @if ($image = $tile->thumbnail())
                            <img src="{{ $image }}" alt="" @if ($loop->first) fetchpriority="high" @endif>
                        @endif
                        <span class="at-tile__caption"><span>{{ $tile->title() }}</span>@if ($price = $home->price($tile, $skin))<span>{{ $price }}</span>@endif</span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    @if ($brands->isNotEmpty())
        <section class="at-band at-band--tight" aria-labelledby="at-brands-title">
            <div class="wrap">
                <h2 id="at-brands-title" class="sk-visually-hidden">{{ $skin->text('brands_title') }}</h2>
                <ul class="at-brands" role="list">
                    @foreach ($brands as $brand)
                        <li>
                            @if ($logo = \Atrium\Admin\Support\MediaUrl::to($brand->logo))
                                <img src="{{ $logo }}" alt="" width="40" height="40" loading="lazy">
                            @endif
                            @if (filled($brand->website))
                                <a href="{{ $brand->website }}" rel="noopener">{{ $brand->name }}</a>
                            @else
                                {{ $brand->name }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($sections !== [])
        <section class="at-band at-band--tight" aria-labelledby="at-sections-title">
            <div class="wrap">
                <div class="at-head">
                    <h2 id="at-sections-title">{{ $skin->text('sections_title') }}</h2>
                </div>
                <ul class="at-bento at-bento--{{ min(count($sections), 5) }}" role="list">
                    @foreach ($sections as $section)
                        <li>
                            @if ($image = $home->sectionPicture($section))
                                <img src="{{ $image }}" alt="" loading="lazy">
                            @endif
                            <a href="{{ $section->url() }}">
                                <span class="at-bento__title">{{ $section->title() }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($products !== [])
        <section class="at-band at-band--soft" aria-labelledby="at-products-title">
            <div class="wrap">
                <div class="at-head">
                    <h2 id="at-products-title">{{ $skin->text('products_title') }}</h2>
                    @if ($catalog)
                        <a class="at-head__all" href="{{ $catalog }}">{{ $skin->text('products_all') }}</a>
                    @endif
                </div>
                <ul class="at-products" role="list">
                    @foreach ($products as $product)
                        @include('atrium-theme::skins.atrium.product-card', ['product' => $product])
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($news !== [])
        <section class="at-band" aria-labelledby="at-news-title">
            <div class="wrap">
                <div class="at-head">
                    <h2 id="at-news-title">{{ $skin->text('news_title') }}</h2>
                    @if ($newsUrl)
                        <a class="at-head__all" href="{{ $newsUrl }}">{{ $skin->text('news_all') }}</a>
                    @endif
                </div>
                <ul class="at-news" role="list">
                    @foreach ($news as $item)
                        @if ($loop->first)
                            <li class="at-news__lead">
                                @if ($image = $item->thumbnail())
                                    <img src="{{ $image }}" alt="" loading="lazy">
                                @endif
                                <div class="at-news__body">
                                    @if ($date = $item->date())
                                        <p class="at-date"><time datetime="{{ $date->toAtomString() }}">{{ $date->isoFormat('LL') }}</time></p>
                                    @endif
                                    <h3><a class="at-link" href="{{ $item->url() }}">{{ $item->title() }}</a></h3>
                                    @if ($teaser = $item->teaser())
                                        <div class="at-teaser">{!! $teaser !!}</div>
                                    @endif
                                </div>
                            </li>
                        @else
                            <li class="at-news__item">
                                @if ($image = $item->thumbnail())
                                    <img src="{{ $image }}" alt="" loading="lazy">
                                @endif
                                <div>
                                    @if ($date = $item->date())
                                        <p class="at-date"><time datetime="{{ $date->toAtomString() }}">{{ $date->isoFormat('LL') }}</time></p>
                                    @endif
                                    <h3><a class="at-link" href="{{ $item->url() }}">{{ $item->title() }}</a></h3>
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($stores !== [])
        <section class="at-band at-band--soft" aria-labelledby="at-stores-title">
            <div class="wrap">
                <div class="at-head">
                    <h2 id="at-stores-title">{{ $skin->text('stores_title') }}</h2>
                    @if ($storesUrl)
                        <a class="at-head__all" href="{{ $storesUrl }}">{{ $skin->text('stores_all') }}</a>
                    @endif
                </div>
                <ul class="at-stores" role="list">
                    @foreach ($stores as $store)
                        @include('atrium-theme::skins.atrium.store-card', ['store' => $store, 'map' => false])
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($team !== [])
        <section class="at-band" aria-labelledby="at-team-title">
            <div class="wrap">
                <div class="at-head">
                    <h2 id="at-team-title">{{ $skin->text('team_title') }}</h2>
                    @if ($teamUrl)
                        <a class="at-head__all" href="{{ $teamUrl }}">{{ $skin->text('team_all') }}</a>
                    @endif
                </div>
                <ul class="at-team" role="list">
                    @foreach ($team as $person)
                        <li class="at-person">
                            @if ($photo = $person->thumbnail())
                                <img src="{{ $photo }}" alt="" width="192" height="192" loading="lazy">
                            @endif
                            <h3><a href="{{ $person->url() }}">{{ $person->title() }}</a></h3>
                            @if (filled($position = $person->record->getAttribute('position')))
                                <p>{{ $position }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($reviews->isNotEmpty())
        <section class="at-band at-band--ink" aria-labelledby="at-reviews-title">
            <div class="wrap">
                @include('atrium-theme::skins.partials.head', ['id' => 'at-reviews-title', 'title' => $skin->text('reviews_title')])
                @include('atrium-theme::skins.partials.reviews')
            </div>
        </section>
    @endif

    @if ($questions->isNotEmpty())
        @include('atrium-theme::skins.partials.faq', ['title' => $skin->text('faq_title')])
    @endif

    @if ($form)
        <section class="at-band at-band--soft" aria-labelledby="at-contact-title">
            <div class="wrap at-contact">
                <div>
                    <h2 id="at-contact-title">{{ $skin->text('contact_title') }}</h2>
                    <p>{{ $skin->text('contact_text') }}</p>
                </div>
                @include('atrium-theme::skins.partials.form', ['code' => $form])
            </div>
        </section>
    @endif
@endsection
