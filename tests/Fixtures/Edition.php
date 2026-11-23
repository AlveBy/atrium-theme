<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures;

/**
 * A module the admin names one way and visitors another.
 */
class Edition extends Post
{
    public static function getAdminLabel(): string
    {
        return 'Edition sections';
    }

    public static function getPublicLabel(): string
    {
        return 'Editions';
    }
}
