{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
<section class="error-page" aria-labelledby="error-title">
    <p class="error-code">{{ $errorCode }}</p>
    <h1 id="error-title">{{ $errorTitle }}</h1>
    {!! $errorBody !!}
</section>
