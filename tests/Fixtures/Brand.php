<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

/**
 * A highload directory: no page of its own.
 */
class Brand extends Model
{
    protected $guarded = [];
}
