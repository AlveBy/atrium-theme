{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
@if ($paginator->hasPages())
    <nav class="pagination" aria-label="{{ __('atrium-theme::theme.pages') }}">
        @if ($url = $paginator->previousPageUrl())
            <a href="{{ $url }}" rel="prev">{{ __('atrium-theme::theme.previous') }}</a>
        @endif
        <span class="current">{{ __('atrium-theme::theme.page_of', ['page' => $paginator->currentPage(), 'total' => $paginator->lastPage()]) }}</span>
        @if ($url = $paginator->nextPageUrl())
            <a href="{{ $url }}" rel="next">{{ __('atrium-theme::theme.next') }}</a>
        @endif
    </nav>
@endif
