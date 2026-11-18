{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{-- Published reviews: stars, text, author and what the review is about. --}}
<ul class="sk-reviews" role="list">
    @foreach ($reviews as $review)
        @php($subject = $home->subject($review))
        <li>
            <figure class="sk-review">
                <span class="sk-stars" role="img" aria-label="{{ __('atrium-theme::skins.rating', ['rating' => $review->rating]) }}">
                    @for ($star = 1; $star <= 5; $star++)
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" @class(['sk-star', 'sk-star--off' => $star > $review->rating])><path d="M12 3l2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 17.3 6.4 20.2l1.1-6.2L3 9.6l6.2-.9z"/></svg>
                    @endfor
                </span>
                <blockquote>{{ $review->text }}</blockquote>
                <figcaption><strong>{{ $review->author_name }}</strong>@if ($subject) · {{ $subject }}@endif</figcaption>
            </figure>
        </li>
    @endforeach
</ul>
