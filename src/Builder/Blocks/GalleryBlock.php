<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Builder\Blocks;

use Atrium\Admin\Fields\GalleryField;
use Atrium\Admin\Support\Cdn;
use Atrium\Admin\Support\MediaUrl;

/**
 * Several pictures in a grid; each opens in full.
 */
final class GalleryBlock extends ThemeBlock
{
    public function label(): string
    {
        return __('atrium-theme::builder.types.gallery');
    }

    public function fields(): array
    {
        return [
            GalleryField::make('images')->label(__('atrium-theme::builder.fields.images'))->required(),
        ];
    }

    public function view(): string
    {
        return 'atrium-theme::builder.blocks.gallery';
    }

    public function data(array $settings): ?array
    {
        $images = [];

        foreach (is_array($settings['images'] ?? null) ? $settings['images'] : [] as $path) {
            $url = MediaUrl::to($path);

            if ($url !== null) {
                $images[] = ['url' => $url, 'thumbnail' => Cdn::image($url, 640)];
            }
        }

        return $images === [] ? null : ['images' => $images];
    }
}
