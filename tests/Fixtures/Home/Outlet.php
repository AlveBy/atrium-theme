<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures\Home;

use Illuminate\Database\Eloquent\Model;

/**
 * Stands in for a store: a name, a phone list and an address.
 */
class Outlet extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['active' => 'boolean', 'phones' => 'array'];
    }
}
