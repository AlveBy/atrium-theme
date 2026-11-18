{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
<div class="wrap sk-footer">
    <span class="sk-footer__brand">{{ mb_strtoupper($siteName) }}.</span>
    <span>{{ $skin->text('tagline') }}</span>
    @if (config('atrium-theme.credit', true))
        <a class="credit" href="https://github.com/AlveBy/atrium">{{ __('atrium-theme::theme.credit') }}</a>
    @endif
</div>
