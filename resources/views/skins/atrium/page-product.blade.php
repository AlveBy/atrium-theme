{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{--
    A product: gallery, name, badge, price, stock, options and features on
    one side; details, text, downloads and related products below. What the
    panel shows is taken out of the record's blocks; the rest follows them.
--}}
@php
    $record = $page->record;
    $blocks = $page->blocks();
    $own = ['article', 'price', 'brand_id', 'brand', 'stores', 'label', 'colors', 'in_stock', 'features', 'related'];
    $items = collect($blocks)->where('kind', 'properties')->flatMap(fn (array $block): array => $block['items'])->keyBy('code');
    $pictures = collect([$page->image()])
        ->merge(collect($blocks)->where('kind', 'images')->flatMap(fn (array $block): array => $block['images']))
        ->filter()->unique()->values();
    $rest = collect($blocks)->reject(fn (array $block): bool => $block['kind'] === 'images')
        ->map(function (array $block) use ($own): array {
            if ($block['kind'] === 'properties') {
                $block['items'] = array_values(array_filter($block['items'], fn (array $item): bool => ! in_array($item['code'], $own, true)));
            }

            return $block;
        })->filter(fn (array $block): bool => $block['kind'] !== 'properties' || $block['items'] !== [])->values()->all();
    $brand = $home->brand($page);
    $badge = $home->badge($page);
    $price = $home->price($page, $skin);
    $stock = filled($record->getAttribute('in_stock'));
    $features = array_values(array_filter((array) $record->getAttribute('features'), fn ($feature): bool => is_string($feature) && $feature !== ''));
    $colors = isset($items['colors']) ? array_filter(array_map('trim', explode(', ', $items['colors']['value']))) : [];
    $stores = $items['stores']['links'] ?? [];
    $article = $items['article']['value'] ?? null;
    $related = $home->related($page, 4);
    $catalog = $home->indexUrl($home::PRODUCT);
    $lead = $page->teaser();
    $text = $page->body();
@endphp
<article class="at-product-page" data-product>
    <div class="at-gallery">
        <div class="at-gallery__main">
            @if ($pictures->isNotEmpty())
                <img src="{{ \Atrium\Admin\Support\Cdn::image($pictures[0], 1280) }}" alt="{{ $page->title() }}" dusk="gallery-main" data-gallery-main fetchpriority="high">
            @endif
        </div>
        @if ($pictures->count() > 1)
            <ul class="at-gallery__thumbs" role="list">
                @foreach ($pictures as $picture)
                    <li><button type="button" dusk="gallery-thumb" data-gallery-src="{{ \Atrium\Admin\Support\Cdn::image($picture, 1280) }}" aria-current="{{ $loop->first ? 'true' : 'false' }}" aria-label="{{ $skin->text('photo', ['number' => $loop->iteration]) }}"><img src="{{ \Atrium\Admin\Support\Cdn::image($picture, 160) }}" alt="" loading="lazy"></button></li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="at-buy">
        <div class="at-buy__top">
            @if ($brand)<span>{{ $brand }}</span>@endif
            @if ($article)<span>{{ $items['article']['label'] }}: {{ $article }}</span>@endif
            @if ($badge)<span class="at-badge">@include('atrium-theme::skins.atrium.icon', ['name' => 'star']){{ $badge }}</span>@endif
        </div>
        <h1>{{ $page->title() }}</h1>
        @if ($lead)
            <div class="at-buy__lead">{!! $lead !!}</div>
        @endif

        <div class="at-buy__panel">
            <div class="at-buy__row">
                @if ($price)<p class="at-price">{{ $price }}</p>@endif
                @if ($stock)
                    <span class="at-stock">@include('atrium-theme::skins.atrium.icon', ['name' => 'tick']){{ $skin->text('in_stock') }}</span>
                @else
                    <span class="at-stock at-stock--out">@include('atrium-theme::skins.atrium.icon', ['name' => 'cross']){{ $skin->text('out_of_stock') }}</span>
                @endif
            </div>
            @if ($colors !== [])
                <div>
                    <p class="at-buy__label">{{ $items['colors']['label'] }}</p>
                    <ul class="at-options" role="list">
                        @foreach ($colors as $color)<li>{{ $color }}</li>@endforeach
                    </ul>
                </div>
            @endif
            @if ($home->hasForm('order'))
                <a class="sk-btn" href="#order">{{ $skin->text('order') }}</a>
            @elseif ($catalog)
                <a class="sk-btn sk-btn--ghost" href="{{ $catalog }}">{{ $skin->text('back_to_catalog') }}</a>
            @endif
        </div>

        @if ($features !== [])
            <ul class="at-ticks" role="list">
                @foreach ($features as $feature)
                    <li>@include('atrium-theme::skins.atrium.icon', ['name' => 'tick'])<span>{{ $feature }}</span></li>
                @endforeach
            </ul>
        @endif

        @if ($stores !== [])
            <div>
                <p class="at-buy__label">{{ $skin->text('available_in') }}</p>
                <ul class="at-options at-options--stores" role="list">
                    @foreach ($stores as $store)
                        <li>@if ($store['url'])<a href="{{ $store['url'] }}">{{ $store['value'] }}</a>@else{{ $store['value'] }}@endif</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</article>

<div class="at-below">
    @if ($text)
        <section>
            <h2>{{ $skin->text('description') }}</h2>
            <div class="at-prose">{!! $text !!}</div>
        </section>
    @endif
    @include('atrium-theme::skins.atrium.blocks', ['blocks' => $rest])

    @if ($related !== [])
        <section aria-labelledby="at-related">
            <h2 id="at-related">{{ $skin->text('related') }}</h2>
            <ul class="at-products" role="list">
                @foreach ($related as $product)
                    @include('atrium-theme::skins.atrium.product-card', ['product' => $product])
                @endforeach
            </ul>
        </section>
    @endif
</div>
<script>
document.addEventListener('click', function (event) {
    var button = event.target.closest('[data-gallery-src]');
    var main = document.querySelector('[data-gallery-main]');
    if (!button || !main) { return; }
    main.src = button.dataset.gallerySrc;
    document.querySelectorAll('[data-gallery-src]').forEach(function (other) { other.setAttribute('aria-current', other === button ? 'true' : 'false'); });
});
</script>
