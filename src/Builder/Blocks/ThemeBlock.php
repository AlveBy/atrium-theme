<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Builder\Blocks;

use Atrium\Admin\Builder\BlockType;

/**
 * What the theme's own block types share.
 */
abstract class ThemeBlock extends BlockType
{
    /**
     * A setting as text; "" when it is missing or of another shape.
     *
     * @param  array<array-key,mixed>  $settings
     */
    protected function text(array $settings, string $name): string
    {
        return is_string($settings[$name] ?? null) ? trim($settings[$name]) : '';
    }

    protected function css(string $name): string
    {
        return dirname(__DIR__, 3)."/resources/css/builder/{$name}.css";
    }
}
