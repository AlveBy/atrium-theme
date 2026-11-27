{{--
    This file is part of Atrium (https://github.com/AlveBy/atrium).
    Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
--}}
{{--
    A page made of blocks (LayoutRenderer): the styles of the block types it
    uses, the grid, and their scripts.
--}}
<div class="atrium-layout">
    <style>{!! $styles !!}</style>
    @foreach ($sections as $section)
        <section class="b-section b-width-{{ $section['width'] }} b-tone-{{ $section['tone'] }}" data-section-id="{{ $section['id'] }}">
            <div class="b-inner">
                @foreach ($section['columns'] as $column)
                    <div class="b-column b-span-{{ $column['span'] }}" data-column-id="{{ $column['id'] }}">
                        @foreach ($column['blocks'] as $block)
                            <div class="b-block b-type-{{ $block['type'] }}" data-block-id="{{ $block['id'] }}">{!! $block['html'] !!}</div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach
    @foreach ($scripts as $script)
        <script>{!! $script !!}</script>
    @endforeach
</div>
