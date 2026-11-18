{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{-- The icons of the advantages, in turn. --}}
@php
    $paths = [
        '<path d="M4 19c4 0 4-6 8-6s4 6 8 6"/><circle cx="6" cy="6" r="2"/><circle cx="18" cy="6" r="2"/>',
        '<path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12z"/>',
        '<path d="M3 12l9-9 9 9-9 9z"/><path d="M12 8v8M9 11h6"/>',
    ];
@endphp
<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">{!! $paths[$index % count($paths)] !!}</svg>
