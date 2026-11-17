<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures;

use Atrium\Admin\Fields\TextField;
use Illuminate\Database\Eloquent\Model;

/**
 * A second tree of sections, with a list of its own look.
 */
class Crate extends Model
{
    protected $guarded = [];

    protected $table = 'crates';

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public static function getAdminLabel(): string
    {
        return 'Crates';
    }

    public static function getAdministrationFields(): array
    {
        return [TextField::make('name')->label('Name')];
    }

    public static function themeView(string $page): ?string
    {
        return $page === 'index' ? 'theme-test::crates' : null;
    }
}
