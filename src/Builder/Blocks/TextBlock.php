<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Builder\Blocks;

use Atrium\Admin\Embeds\Embeds;
use Atrium\Admin\Fields\ContentField;
use Atrium\Admin\Support\Cdn;

/**
 * A rich text; forms and blocks put into it from the editor are drawn.
 */
final class TextBlock extends ThemeBlock
{
    public function label(): string
    {
        return __('atrium-theme::builder.types.text');
    }

    public function fields(): array
    {
        return [
            ContentField::make('html')
                ->label(__('atrium-theme::builder.fields.html'))
                ->modes(['wysiwyg', 'html'])
                ->required(),
        ];
    }

    public function translatable(): array
    {
        return ['html'];
    }

    public function view(): string
    {
        return 'atrium-theme::builder.blocks.text';
    }

    public function data(array $settings): ?array
    {
        $html = $this->text($settings, 'html');

        return $html === '' ? null : ['html' => app(Embeds::class)->render(Cdn::html($html))];
    }
}
