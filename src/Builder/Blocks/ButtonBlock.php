<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Builder\Blocks;

use Atrium\Admin\Fields\CheckboxField;
use Atrium\Admin\Fields\LinkField;
use Atrium\Admin\Fields\SelectField;
use Atrium\Admin\Fields\TextField;
use Atrium\Theme\Builder\BlockLinks;

/**
 * A link that looks like a button: filled, or only outlined.
 */
final class ButtonBlock extends ThemeBlock
{
    private const STYLES = ['primary', 'secondary'];

    public function label(): string
    {
        return __('atrium-theme::builder.types.button');
    }

    public function fields(): array
    {
        $styles = [];

        foreach (self::STYLES as $style) {
            $styles[$style] = __("atrium-theme::builder.styles.{$style}");
        }

        return [
            TextField::make('label')->label(__('atrium-theme::builder.fields.label'))->required()->maxLength(255),
            LinkField::make('link')->label(__('atrium-theme::builder.fields.link'))->required(),
            SelectField::make('style')->label(__('atrium-theme::builder.fields.style'))->options($styles)->default(self::STYLES[0]),
            CheckboxField::make('new_tab')->label(__('atrium-theme::builder.fields.new_tab'))->default(false),
        ];
    }

    public function translatable(): array
    {
        return ['label'];
    }

    public function view(): string
    {
        return 'atrium-theme::builder.blocks.button';
    }

    public function data(array $settings): ?array
    {
        $label = $this->text($settings, 'label');
        $href = app(BlockLinks::class)->url($settings['link'] ?? null);

        if ($label === '' || $href === null) {
            return null;
        }

        $style = $this->text($settings, 'style');

        return [
            'label' => $label,
            'href' => $href,
            'style' => in_array($style, self::STYLES, true) ? $style : self::STYLES[0],
            'newTab' => (bool) ($settings['new_tab'] ?? false),
        ];
    }

    public function styles(): array
    {
        return [$this->css('button')];
    }
}
