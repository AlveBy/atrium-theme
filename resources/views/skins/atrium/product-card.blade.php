{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{-- One product in a grid: picture, badge, brand, name, price and stock. --}}
@php
    $brand = $home->brand($product);
    $badge = $home->badge($product);
    $price = $home->price($product, $skin);
    $stock = filled($product->record->getAttribute('in_stock'));
@endphp
<li class="at-product" dusk="product" data-brand="{{ $brand }}" data-badge="{{ $badge }}" data-stock="{{ $stock ? 1 : 0 }}" data-price="{{ (float) $product->record->getAttribute('price') }}">
    <div class="at-product__image">
        @if ($image = $product->thumbnail())
            <img src="{{ $image }}" alt="" loading="lazy">
        @endif
        @if ($badge)
            <span class="at-badge">@include('atrium-theme::skins.atrium.icon', ['name' => 'star']){{ $badge }}</span>
        @endif
    </div>
    @if ($brand)
        <p class="at-product__brand">{{ $brand }}</p>
    @endif
    <h3><a href="{{ $product->url() }}">{{ $product->title() }}</a></h3>
    @if ($teaser = $product->teaser())
        <div class="at-product__teaser">{!! $teaser !!}</div>
    @endif
    <div class="at-product__foot">
        @if ($price)
            <p class="at-price">{{ $price }}</p>
        @endif
        @if ($stock)
            <span class="at-stock">@include('atrium-theme::skins.atrium.icon', ['name' => 'tick']){{ $skin->text('in_stock') }}</span>
        @endif
    </div>
</li>
