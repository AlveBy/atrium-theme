{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{-- Small line icons, decoration only: the words beside them carry the meaning. --}}
@switch($name)
    @case('tick')
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 12.5 9.5 18 20 6.5"/></svg>
        @break
    @case('cross')
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6 6 18"/></svg>
        @break
    @case('pin')
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.800 12 21 12 21z"/><circle cx="12" cy="9.500" r="2.500"/></svg>
        @break
    @case('clock')
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.500 2"/></svg>
        @break
    @case('phone')
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 4h4l2 5-2.500 1.500a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A15 15 0 0 1 3 6a2 2 0 0 1 2-2z"/></svg>
        @break
    @case('mail')
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="m4 7.500 8 6 8-6"/></svg>
        @break
    @case('star')
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" class="at-badge-star"><path d="M12 3l2.800 5.700 6.200.9-4.500 4.400 1.100 6.200L12 17.300 6.400 20.200l1.100-6.200L3 9.600l6.200-.9z"/></svg>
        @break
@endswitch
