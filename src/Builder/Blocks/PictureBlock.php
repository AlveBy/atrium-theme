<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Builder\Blocks;

use Atrium\Admin\Fields\ImageField;
use Atrium\Admin\Fields\LinkField;
use Atrium\Admin\Fields\TextField;
use Atrium\Admin\Support\Cdn;
use Atrium\Admin\Support\MediaUrl;
use Atrium\Theme\Builder\BlockLinks;

/**
 * One picture, with a caption and a link if wanted. Resized by the CDN or
 * imgproxy when an image driver is set, as the theme's other pictures are.
 */
final class PictureBlock extends ThemeBlock
{
    public function label(): string
    {
        return __('atrium-theme::builder.types.picture');
    }

    public function fields(): array
    {
        return [
            ImageField::make('image')->label(__('atrium-theme::builder.fields.image'))->required(),
            TextField::make('alt')->label(__('atrium-theme::builder.fields.alt'))->maxLength(255),
            TextField::make('caption')->label(__('atrium-theme::builder.fields.caption'))->maxLength(255),
            LinkField::make('link')->label(__('atrium-theme::builder.fields.link')),
        ];
    }

    public function translatable(): array
    {
        return ['alt', 'caption'];
    }

    public function view(): string
    {
        return 'atrium-theme::builder.blocks.picture';
    }

    public function data(array $settings): ?array
    {
        $url = MediaUrl::to($settings['image'] ?? null);

        if ($url === null) {
            return null;
        }

        $href = app(BlockLinks::class)->url($settings['link'] ?? null);
        $alt = $this->text($settings, 'alt');
        $caption = $this->text($settings, 'caption');

        // A link has to have a name, even around a picture nobody described.
        if ($alt === '' && $href !== null) {
            $alt = $caption !== '' ? $caption : $href;
        }

        return [
            'src' => Cdn::image($url, 1280),
            'srcset' => Cdn::srcset($url, 1920),
            'alt' => $alt,
            'caption' => $caption,
            'href' => $href,
        ];
    }

    public function styles(): array
    {
        return [$this->css('picture')];
    }
}
