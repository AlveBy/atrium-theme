{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
<figure class="b-map">
    <iframe class="b-map-frame" src="{{ $src }}" title="{{ $address !== '' ? $address : __('atrium-theme::builder.map.title') }}" loading="lazy"></iframe>
    <figcaption>
        @if ($address !== '')
            <span class="b-map-address">{{ $address }}</span>
        @endif
        <a href="{{ $href }}" rel="noopener">{{ __('atrium-theme::builder.map.open') }}</a>
    </figcaption>
</figure>
