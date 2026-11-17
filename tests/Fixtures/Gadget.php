<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures;

use Atrium\Admin\Fields\BelongsToField;
use Atrium\Admin\Fields\BelongsToManyField;
use Atrium\Admin\Fields\DateField;
use Atrium\Admin\Fields\DateTimeField;
use Atrium\Admin\Fields\GalleryField;
use Atrium\Admin\Fields\TextareaField;
use Atrium\Admin\Fields\TextField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A module with the kinds of values a page shows as properties: dates
 * kept as text, lists, notes and links that may lead nowhere.
 */
class Gadget extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['shots' => 'array', 'sizes' => 'array', 'owner' => 'array'];
    }

    public function maker(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand');
    }

    public static function getAdministrationFields(): array
    {
        return [
            TextField::make('name')->label('Name'),
            TextField::make('title')->label('Title'),
            TextareaField::make('notes')->label('Notes'),
            DateField::make('released_on')->label('Released on'),
            DateTimeField::make('released_at')->label('Released at'),
            TextField::make('sizes')->label('Sizes'),
            GalleryField::make('shots')->label('Shots'),
            BelongsToField::make('brand')->label('Brand')->model(Brand::class),
            BelongsToField::make('owner')->label('Owner')->model(Brand::class),
            BelongsToField::make('keeper')->label('Keeper'),
            BelongsToManyField::make('maker')->label('Maker')->model(Brand::class),
            BelongsToManyField::make('parts')->label('Parts')->model(Brand::class),
        ];
    }
}
