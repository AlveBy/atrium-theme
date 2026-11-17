<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

/**
 * A record that is never saved: whatever fields and values a test hands it
 * are what the page has to work from.
 */
class Relic extends Model
{
    protected $table = 'posts';

    protected $guarded = [];

    /** @var array<int|string,mixed> */
    public static array $fields = [];

    /**
     * @return array<int|string,mixed>
     */
    public static function getAdministrationFields(): array
    {
        return static::$fields;
    }
}
