<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures;

/**
 * A module with a page of its own look: the theme keeps the rest.
 */
class Story extends Post
{
    protected $table = 'posts';

    public static function getAdminLabel(): string
    {
        return 'Stories';
    }

    public static function themeView(string $page): ?string
    {
        return match ($page) {
            'page' => 'theme-test::story',
            'index' => 'theme-test::missing',
            default => null,
        };
    }
}
