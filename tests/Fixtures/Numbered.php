<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

/**
 * A module whose label is a number, as a lazy one-liner might make it.
 */
class Numbered extends Model
{
    protected $table = 'posts';

    public static function getAdminLabel(): int
    {
        return 7;
    }
}
