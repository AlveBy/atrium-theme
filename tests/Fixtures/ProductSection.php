<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures;

use Atrium\Admin\Fields\ContentField;
use Atrium\Admin\Fields\TextField;
use Illuminate\Database\Eloquent\Model;

class ProductSection extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public static function getAdminLabel(): string
    {
        return 'Product sections';
    }

    public static function getAdministrationFields(): array
    {
        return [
            TextField::make('name')->label('Name'),
            ContentField::make('description')->label('Description'),
        ];
    }
}
