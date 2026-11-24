{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{--
    Home goods shop. The hero is the page "home" (Pages), then the catalog's
    sections and products with their brands, the brands and the stores.
--}}
@extends('atrium-theme::layout')

@section('main-class', 'sk-home')

@section('content')
    @php
        $hero = $home->page('home');
        $catalog = $home->indexUrl($home::PRODUCT);
        $sections = $home->sections();
        $products = $home->products(8);
        $brands = $home->brands();
        $stores = $home->stores();
    @endphp

    <section class="sk-hero wrap" aria-labelledby="sk-hero-title">
        <div class="sk-hero__text">
            <p class="sk-eyebrow">{{ $skin->text('eyebrow') }}</p>
            <h1 id="sk-hero-title">{{ $hero?->name ?: $siteName }}</h1>
            @if ($hero && filled($hero->content))
                <div class="sk-lead">@embeds($hero->content)</div>
            @endif
            @if ($catalog)
                <a class="sk-btn sk-btn--ink" href="{{ $catalog }}">{{ $skin->text('catalog_button') }}</a>
            @endif
        </div>
        <div class="sk-hero__art sk-hero__art--brand">{{ $skin->illustration('shop-shelf') }}</div>
    </section>

    @if ($products !== [])
        <section class="sk-band sk-band--tight" aria-labelledby="sk-products-title">
            <div class="wrap">
                <h2 id="sk-products-title" class="sk-visually-hidden">{{ $skin->text('products_title') }}</h2>
                @if ($sections !== [])
                    <nav class="sk-chips" aria-label="{{ $skin->text('sections') }}">
                        <a class="sk-chips__all" href="{{ $catalog }}">{{ $skin->text('all') }}</a>
                        @foreach ($sections as $section)
                            <a href="{{ $section->url() }}">{{ $section->title() }}</a>
                        @endforeach
                    </nav>
                @endif
                <ul class="sk-products" role="list">
                    @foreach ($products as $product)
                        <li class="sk-product">
                            <div class="sk-product__image">
                                @if ($image = $product->thumbnail())
                                    <img src="{{ $image }}" alt="" loading="lazy">
                                @endif
                                @if ($badge = $home->badge($product))
                                    <span class="sk-badge">{{ $badge }}</span>
                                @endif
                            </div>
                            @if ($brand = $home->brand($product))
                                <span class="sk-product__brand">{{ $brand }}</span>
                            @endif
                            <h3><a href="{{ $product->url() }}">{{ $product->title() }}</a></h3>
                            @if ($price = $home->price($product, $skin))
                                <p class="sk-price">{{ $price }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($brands->isNotEmpty())
        <section class="sk-band sk-band--surface" aria-labelledby="sk-brands-title">
            <div class="wrap">
                <h2 id="sk-brands-title">{{ $skin->text('brands_title') }}</h2>
                <ul class="sk-brands" role="list">
                    @foreach ($brands as $brand)
                        <li class="sk-brands__item sk-brands__item--{{ $loop->index % 3 }}">
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

    @if ($stores !== [])
        <section class="sk-band" aria-labelledby="sk-stores-title">
            <div class="wrap">
                <h2 id="sk-stores-title">{{ $skin->text('stores_title') }}</h2>
                <ul class="sk-stores" role="list">
                    @foreach ($stores as $store)
                        <li class="sk-store">
                            <h3><a href="{{ $store->url() }}">{{ $store->title() }}</a></h3>
                            <p>{{ $home->address($store) }}</p>
                            @if ($hours = $home->hours($store))
                                <p class="sk-mono sk-muted">{{ $hours }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif
@endsection
