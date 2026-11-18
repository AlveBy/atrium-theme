{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{--
    Restaurant. The hero is the page "home" (Pages), the menu is the
    catalog by its top sections, the quote is the newest review, the
    addresses are the stores, then the table booking form.
--}}
@extends('atrium-theme::layout')

@section('main-class', 'sk-home')

@section('content')
    @php
        $hero = $home->page('home');
        $title = (string) ($hero?->name ?: $siteName);
        $words = preg_split('/\s+/u', trim($title)) ?: [];
        $last = count($words) > 1 ? array_pop($words) : null;
        $menu = $home->menu(6);
        $quote = $home->reviews(1)->first();
        $stores = $home->stores();
        $delivery = $home->page('delivery');
    @endphp

    <section class="sk-hero wrap" aria-labelledby="sk-hero-title">
        <div class="sk-hero__text">
            <p class="sk-eyebrow">{{ $skin->text('eyebrow') }}</p>
            <h1 id="sk-hero-title">{{ implode(' ', $words) }}@if ($last) <em>{{ $last }}</em>@endif</h1>
            @if ($hero && filled($hero->content))
                <div class="sk-lead">{!! $hero->content !!}</div>
            @endif
            <div class="sk-actions">
                @if ($menuUrl = $home->indexUrl($home::PRODUCT))
                    <a class="sk-btn" href="{{ $menuUrl }}">{{ $skin->text('hero_menu') }}</a>
                @endif
                @if ($delivery && ($deliveryUrl = $home->pageUrl($delivery)))
                    <a class="sk-btn sk-btn--ghost" href="{{ $deliveryUrl }}">{{ $delivery->name }}</a>
                @endif
            </div>
        </div>
        <div class="sk-hero__art">{{ $skin->illustration('restaurant-mains') }}</div>
    </section>

    @if ($menu !== [])
        <section class="sk-band sk-band--line" aria-labelledby="sk-menu-title">
            <div class="wrap sk-split">
                <div class="sk-split__title">
                    <h2 id="sk-menu-title">{{ $skin->text('menu_title') }}</h2>
                    <p class="sk-muted">{{ $skin->text('menu_text') }}</p>
                    @if (count($menu) > 1)
                        <div class="sk-tabs" data-sk-tabs>
                            @foreach ($menu as $group)
                                <button type="button" class="sk-tab" aria-controls="sk-menu-{{ $loop->index }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}">{{ $group['section']->title() }}</button>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="sk-menu">
                    @foreach ($menu as $group)
                        <div id="sk-menu-{{ $loop->index }}" class="sk-menu__group">
                            <h3 class="sk-menu__section"><a href="{{ $group['section']->url() }}">{{ $group['section']->title() }}</a></h3>
                            <ul role="list">
                                @foreach ($group['products'] as $dish)
                                    <li class="sk-dish">
                                        <div class="sk-dish__line">
                                            <a class="sk-dish__name" href="{{ $dish->url() }}">{{ $dish->title() }}</a>
                                            <span class="sk-dish__dots" aria-hidden="true"></span>
                                            @if ($price = $home->price($dish, $skin))
                                                <span class="sk-dish__price">{{ $price }}</span>
                                            @endif
                                        </div>
                                        @if ($teaser = $dish->teaser())
                                            <div class="sk-dish__text">{!! $teaser !!}</div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        <script>
        (function () {
            var tabs = document.querySelector('[data-sk-tabs]');
            if (!tabs) return;
            var buttons = tabs.querySelectorAll('button');
            function show(button) {
                buttons.forEach(function (other) {
                    var on = other === button;
                    other.setAttribute('aria-pressed', on ? 'true' : 'false');
                    document.getElementById(other.getAttribute('aria-controls')).hidden = !on;
                });
            }
            buttons.forEach(function (button) { button.addEventListener('click', function () { show(button); }); });
            show(buttons[0]);
            tabs.closest('.sk-split').setAttribute('data-sk-ready', '');
        })();
        </script>
    @endif

    @if ($quote)
        <section class="sk-band sk-band--surface" aria-label="{{ __('atrium-theme::skins.review') }}">
            <figure class="wrap sk-quote">
                <blockquote>«{{ $quote->text }}»</blockquote>
                <figcaption>{{ $quote->author_name }}@if ($subject = $home->subject($quote)), {{ $subject }}@endif</figcaption>
            </figure>
        </section>
    @endif

    @if ($stores !== [])
        <section class="sk-band" aria-labelledby="sk-stores-title">
            <div class="wrap">
                <h2 id="sk-stores-title">{{ $skin->text('stores_title') }}</h2>
                <ul class="sk-stores" role="list">
                    @foreach ($stores as $store)
                        <li class="sk-store">
                            <h3>{{ $store->title() }}</h3>
                            <p>{{ $home->address($store) }}</p>
                            @if ($hours = $home->hours($store))
                                <p class="sk-muted">{{ $hours }}</p>
                            @endif
                            <a href="{{ $store->url() }}">{{ $skin->text('directions') }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($home->hasForm('table-booking'))
        <section id="booking" class="sk-band sk-band--line" aria-labelledby="sk-booking-title">
            <div class="wrap">
                <h2 id="sk-booking-title">{{ $skin->text('booking_title') }}</h2>
                <p class="sk-muted">{{ $skin->text('booking_text') }}</p>
                @include('atrium-theme::skins.partials.form', ['code' => 'table-booking', 'class' => 'sk-form--inline'])
            </div>
        </section>
    @endif
@endsection
