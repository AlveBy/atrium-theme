{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{--
    A module's list and a section, for every module the theme draws:
    products with filters, news, stores with maps, anything else as cards.
    A module that names its own view (team, questions) keeps it.
--}}
@extends('atrium-theme::layout', [
    'metaTitle' => isset($page) ? $page->seoTitle() : $title,
    'metaDescription' => isset($page) ? $page->seoDescription() : null,
    'metaKeywords' => isset($page) ? $page->seoKeywords() : null,
    'ogImage' => isset($page) ? $page->ogImage() : null,
])

@php
    $home = app(\Atrium\Theme\Skins\HomeContent::class);
    $first = $records?->first()?->record;
    $kind = match (true) {
        $first === null => 'none',
        $first instanceof ($home::PRODUCT) => 'products',
        $first instanceof ($home::STORE) => 'stores',
        $first instanceof ($home::NEWS) => 'news',
        default => 'cards',
    };
    $brands = $kind === 'products' ? $records->getCollection()->map(fn ($record) => $home->brand($record))->filter()->unique()->sort()->values() : collect();
    $stocked = $kind === 'products' && $records->getCollection()->contains(fn ($record) => filled($record->record->getAttribute('in_stock')));
@endphp

@section('content')
    <div class="at-page-title">
        <h1>{{ $title }}</h1>
        @if ($records !== null && $records->total() > 0)
            <p class="at-count">{{ $skin->text('count', ['count' => $records->total()]) }}</p>
        @endif
    </div>

    @if ($text)
        <div class="at-section-text">{!! $text !!}</div>
    @endif

    @if ($sections !== [])
        <nav aria-label="{{ $skin->text('sections') }}">
            <ul class="at-chips">
                @foreach ($sections as $section)
                    <li><a href="{{ $section->url() }}">{{ $section->title() }}</a></li>
                @endforeach
            </ul>
        </nav>
    @endif

    @if ($records !== null && $records->isNotEmpty())
        @if ($kind === 'products')
            <div data-filter-list>
                @if ($brands->count() > 1 || $stocked)
                    <div class="at-filters" role="group" aria-label="{{ $skin->text('filters') }}">
                        @if ($brands->count() > 1)
                            <fieldset>
                                <legend>{{ $skin->text('filter_brand') }}</legend>
                                <ul class="at-chips" role="list">
                                    <li><button type="button" data-brand="" aria-pressed="true">{{ $skin->text('filter_all') }}</button></li>
                                    @foreach ($brands as $brand)
                                        <li><button type="button" dusk="brand-filter" data-brand="{{ $brand }}" aria-pressed="false">{{ $brand }}</button></li>
                                    @endforeach
                                </ul>
                            </fieldset>
                        @endif
                        @if ($stocked)
                            <ul class="at-chips" role="list">
                                <li><button type="button" data-stock aria-pressed="false">{{ $skin->text('filter_stock') }}</button></li>
                            </ul>
                        @endif
                        <label class="at-filters__sort">{{ $skin->text('sort') }}
                            <select data-sort>
                                <option value="">{{ $skin->text('sort_default') }}</option>
                                <option value="asc">{{ $skin->text('sort_cheap') }}</option>
                                <option value="desc">{{ $skin->text('sort_dear') }}</option>
                            </select>
                        </label>
                    </div>
                    <p class="sk-visually-hidden" role="status" aria-live="polite" data-status data-text="{{ $skin->text('shown', ['shown' => ':shown', 'total' => ':total']) }}"></p>
                @endif
                <ul class="at-products" role="list" dusk="products">
                    @foreach ($records as $product)
                        @include('atrium-theme::skins.atrium.product-card', ['product' => $product])
                    @endforeach
                </ul>
                <p class="at-empty" data-none hidden>{{ $skin->text('filter_none') }}</p>
            </div>
            <script>
            (function () {
                var root = document.querySelector('[data-filter-list]');
                var bar = root && root.querySelector('.at-filters');
                if (!bar) { return; }
                var grid = root.querySelector('.at-products');
                var items = Array.prototype.slice.call(grid.children);
                var position = new Map(items.map(function (item, index) { return [item, index]; }));
                var status = root.querySelector('[data-status]');
                var none = root.querySelector('[data-none]');
                var stock = bar.querySelector('[data-stock]');
                var sort = bar.querySelector('[data-sort]');
                var brand = '';
                bar.setAttribute('data-ready', '');
                function apply() {
                    var shown = 0;
                    items.forEach(function (item) {
                        var visible = (brand === '' || item.dataset.brand === brand) && !(stock && stock.getAttribute('aria-pressed') === 'true' && item.dataset.stock !== '1');
                        item.hidden = !visible;
                        shown += visible ? 1 : 0;
                    });
                    var direction = sort.value;
                    items.slice().sort(function (a, b) {
                        return direction === '' ? position.get(a) - position.get(b) : (direction === 'asc' ? 1 : -1) * (a.dataset.price - b.dataset.price);
                    }).forEach(function (item) { grid.appendChild(item); });
                    none.hidden = shown !== 0;
                    status.textContent = status.dataset.text.replace(':shown', shown).replace(':total', items.length);
                }
                bar.addEventListener('click', function (event) {
                    var button = event.target.closest('button');
                    if (!button) { return; }
                    if (button.hasAttribute('data-stock')) {
                        button.setAttribute('aria-pressed', button.getAttribute('aria-pressed') === 'true' ? 'false' : 'true');
                    } else if (button.hasAttribute('data-brand')) {
                        brand = button.dataset.brand;
                        bar.querySelectorAll('[data-brand]').forEach(function (other) { other.setAttribute('aria-pressed', other === button ? 'true' : 'false'); });
                    }
                    apply();
                });
                sort.addEventListener('change', apply);
            })();
            </script>
        @elseif ($kind === 'stores')
            <ul class="at-stores" role="list">
                @foreach ($records as $store)
                    @include('atrium-theme::skins.atrium.store-card', ['store' => $store, 'map' => true])
                @endforeach
            </ul>
        @elseif ($kind === 'news')
            <ul class="at-cards" role="list">
                @foreach ($records as $item)
                    @include('atrium-theme::skins.atrium.card', ['record' => $item, 'heading' => 'h2'])
                @endforeach
            </ul>
        @else
            <ul class="at-cards" role="list">
                @foreach ($records as $record)
                    @include('atrium-theme::skins.atrium.card', ['record' => $record, 'heading' => 'h2'])
                @endforeach
            </ul>
        @endif
        @include('atrium-theme::partials.pagination', ['paginator' => $records])
    @elseif ($sections === [])
        <p class="at-empty">{{ __('atrium-theme::theme.empty') }}</p>
    @endif
@endsection
