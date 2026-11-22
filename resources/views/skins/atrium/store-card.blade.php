{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{--
    One store: name, address, hours, phones, e-mail and, when $map is on,
    its map. The map is OpenStreetMap, loaded only when the visitor asks
    for it, so a page costs no request to another site until then; without
    JavaScript the "Open the map" link does the same job.
--}}
@php
    $address = $home->address($store);
    $hours = $home->hours($store);
    $phones = array_filter((array) $store->record->getAttribute('phones'), 'is_string');
    $email = $store->record->getAttribute('email');
    $location = $store->record->getAttribute('location');
    $lat = is_array($location) && is_numeric($location['lat'] ?? null) ? (float) $location['lat'] : null;
    $lng = is_array($location) && is_numeric($location['lng'] ?? null) ? (float) $location['lng'] : null;
    $mapUrl = $lat !== null && $lng !== null ? 'https://www.openstreetmap.org/?mlat='.$lat.'&mlon='.$lng.'#map='.'17/'.$lat.'/'.$lng : null;
    $embed = $lat !== null && $lng !== null ? 'https://www.openstreetmap.org/export/embed.html?bbox='.($lng - 0.008).'%2C'.($lat - 0.005).'%2C'.($lng + 0.008).'%2C'.($lat + 0.005).'&layer=mapnik&marker='.$lat.'%2C'.$lng : null;
@endphp
<li class="at-store">
    @if (empty($single))
        <h3><a href="{{ $store->url() }}">{{ $store->title() }}</a></h3>
    @endif
    <ul class="at-facts" role="list">
        @if ($address !== '')
            <li>@include('atrium-theme::skins.atrium.icon', ['name' => 'pin'])<span>{{ $address }}</span></li>
        @endif
        @if ($hours)
            <li>@include('atrium-theme::skins.atrium.icon', ['name' => 'clock'])<span>{{ $hours }}</span></li>
        @endif
        @foreach ($phones as $phone)
            <li>@include('atrium-theme::skins.atrium.icon', ['name' => 'phone'])<a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ $phone }}</a></li>
        @endforeach
        @if (filled($email))
            <li>@include('atrium-theme::skins.atrium.icon', ['name' => 'mail'])<a href="mailto:{{ $email }}">{{ $email }}</a></li>
        @endif
    </ul>
    @if ($map && $embed)
        <div class="at-map" data-map="{{ $embed }}" data-title="{{ $skin->text('map_title', ['store' => $store->title()]) }}">
            <button type="button" class="at-map__open">@include('atrium-theme::skins.atrium.icon', ['name' => 'pin']){{ $skin->text('map_show') }}</button>
        </div>
    @endif
    @if ($mapUrl)
        <p class="at-store__links"><a href="{{ $mapUrl }}" rel="noopener">{{ $skin->text('map_open') }}</a></p>
    @endif
</li>
@once
    @if ($map)
        <script>
        document.addEventListener('click', function (event) {
            var button = event.target.closest('.at-map__open');
            if (!button) { return; }
            var box = button.closest('.at-map');
            var frame = document.createElement('iframe');
            frame.src = box.dataset.map;
            frame.title = box.dataset.title;
            frame.loading = 'lazy';
            frame.referrerPolicy = 'no-referrer';
            box.replaceChildren(frame);
        });
        </script>
    @endif
@endonce
