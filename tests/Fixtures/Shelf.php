<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures;

/**
 * A section with a page of its own look, and nothing else of its own.
 */
class Shelf extends ProductSection
{
    protected $table = 'product_sections';

    public static function themeView(string $page): ?string
    {
        return $page === 'section' ? 'theme-test::shelf' : null;
    }
}
