{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{-- A section heading with a link to the whole list. --}}
<div class="sk-head">
    <h2 id="{{ $id }}">{{ $title }}</h2>
    @if (! empty($url))
        <a class="sk-head__all" href="{{ $url }}">{{ $all }}</a>
    @endif
</div>
