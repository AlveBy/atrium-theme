{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
<div class="gallery">
    @foreach ($images as $image)
        <a href="{{ $image['url'] }}"><img src="{{ $image['thumbnail'] }}" alt="{{ __('atrium-theme::builder.image', ['number' => $loop->iteration]) }}" loading="lazy"></a>
    @endforeach
</div>
