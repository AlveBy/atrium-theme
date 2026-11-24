{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{-- The fields of a record below its text, in the skin's shapes: $blocks as RecordView::blocks() gives them. --}}
@foreach ($blocks as $block)
    @switch($block['kind'])
        @case('properties')
            <section>
                <h2>{{ $skin->text('details') }}</h2>
                <dl class="at-specs">
                    @foreach ($block['items'] as $item)
                        <dt>{{ $item['label'] }}</dt>
                        @if ($item['links'])
                            <dd>@foreach ($item['links'] as $link)@if ($link['url'])<a href="{{ $link['url'] }}">{{ $link['value'] }}</a>@else{{ $link['value'] }}@endif{{ $loop->last ? '' : ', ' }}@endforeach</dd>
                        @else
                            <dd>@if ($item['url'])<a href="{{ $item['url'] }}">{{ $item['value'] }}</a>@else{{ $item['value'] }}@endif</dd>
                        @endif
                    @endforeach
                </dl>
            </section>
            @break

        @case('html')
            <section>
                <h2>{{ $block['label'] }}</h2>
                <div class="at-prose">@embeds($block['html'])</div>
            </section>
            @break

        @case('images')
            <section>
                <h2>{{ $block['label'] }}</h2>
                <div class="at-photos">
                    @foreach ($block['images'] as $image)
                        <a href="{{ $image }}"><img src="{{ \Atrium\Admin\Support\Cdn::image($image, 640) }}" alt="{{ $block['label'] }} {{ $loop->iteration }}" loading="lazy"></a>
                    @endforeach
                </div>
            </section>
            @break

        @case('files')
            <section>
                <h2>{{ $block['label'] }}</h2>
                <ul class="at-files" role="list">
                    @foreach ($block['files'] as $file)
                        <li><a href="{{ $file['url'] }}" download><span class="at-files__type">{{ pathinfo($file['name'], PATHINFO_EXTENSION) ?: '—' }}</span><span>{{ $file['name'] }}</span></a></li>
                    @endforeach
                </ul>
            </section>
            @break
    @endswitch
@endforeach
