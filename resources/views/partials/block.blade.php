{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{-- One part of a record: properties, a text, pictures or files. --}}
@switch($block['kind'])
    @case('properties')
        <dl class="properties">
            @foreach ($block['items'] as $item)
                <dt>{{ $item['label'] }}</dt>
                @if ($item['links'])
                    <dd>@foreach ($item['links'] as $link)@if ($link['url'])<a href="{{ $link['url'] }}">{{ $link['value'] }}</a>@else{{ $link['value'] }}@endif{{ $loop->last ? '' : ', ' }}@endforeach</dd>
                @else
                    <dd>@if ($item['url'])<a href="{{ $item['url'] }}">{{ $item['value'] }}</a>@else{{ $item['value'] }}@endif</dd>
                @endif
            @endforeach
        </dl>
        @break

    @case('html')
        <section>
            <h2>{{ $block['label'] }}</h2>
            <div class="content">{!! $block['html'] !!}</div>
        </section>
        @break

    @case('images')
        <section>
            <h2>{{ $block['label'] }}</h2>
            <div class="gallery">
                @foreach ($block['images'] as $image)
                    <a href="{{ $image }}"><img src="{{ \Atrium\Admin\Support\Cdn::image($image, 640) }}" alt="{{ $block['label'] }} {{ $loop->iteration }}" loading="lazy"></a>
                @endforeach
            </div>
        </section>
        @break

    @case('files')
        <section>
            <h2>{{ $block['label'] }}</h2>
            <ul class="files">
                @foreach ($block['files'] as $file)
                    <li><a href="{{ $file['url'] }}" download>{{ $file['name'] }}</a></li>
                @endforeach
            </ul>
        </section>
        @break
@endswitch
