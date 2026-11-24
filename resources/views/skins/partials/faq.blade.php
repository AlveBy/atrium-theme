{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{-- Questions of the FAQ module, the heading beside them. --}}
{{ app('Atrium\Faq\Faq')->jsonLd($questions) }}
<section class="sk-band sk-faq" aria-labelledby="sk-faq-title">
    <div class="wrap sk-split">
        <h2 id="sk-faq-title" class="sk-split__title">{{ $title }}</h2>
        <div class="sk-faq__list">
            @foreach ($questions as $question)
                <details class="sk-faq__item">
                    <summary>{{ $question->translated('question') }}</summary>
                    <div class="sk-faq__answer">@embeds($question->translated('answer'))</div>
                </details>
            @endforeach
        </div>
    </div>
</section>
