{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
<figure class="b-picture">
    @if ($href !== null)<a href="{{ $href }}">@endif
    <img src="{{ $src }}" @if ($srcset !== null) srcset="{{ $srcset }}" sizes="(max-width: 40rem) 100vw, 72rem" @endif alt="{{ $alt }}" loading="lazy">
    @if ($href !== null)</a>@endif
    @if ($caption !== '')
        <figcaption>{{ $caption }}</figcaption>
    @endif
</figure>
