<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures;

use Atrium\Admin\Cache\CachesRecords;
use Atrium\Admin\Contracts\Cacheable;
use Atrium\Admin\Fields\BelongsToField;
use Atrium\Admin\Fields\BelongsToManyField;
use Atrium\Admin\Fields\SlugField;
use Atrium\Admin\Fields\TextField;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use RuntimeException;

/**
 * A cached module whose form links to other records every way a page
 * can: a list of them first, then a link by id (with the relation behind
 * it), links by another column and a link no relation stands behind.
 */
class Parcel extends Model implements Cacheable
{
    use CachesRecords;

    /** Whether the name of a record called "boom" cannot be read. */
    public static bool $explode = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    protected function name(): Attribute
    {
        return Attribute::get(fn (?string $name): ?string => self::$explode && $name === 'boom' ? throw new RuntimeException('The name cannot be read.') : $name);
    }

    public static function getAdminLabel(): string
    {
        return 'Parcels';
    }

    /**
     * @return BelongsToMany<Brand,$this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'parcel_brand', 'parcel_id', 'brand_id');
    }

    /**
     * @return BelongsTo<Brand,$this>
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    /**
     * @return BelongsTo<Brand,$this>
     */
    public function maker(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'maker_id', 'xml_id');
    }

    /**
     * A relation of another kind than the form says.
     *
     * @return BelongsTo<Brand,$this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'owner_id');
    }

    public static function getAdministrationFields(): array
    {
        return [
            TextField::make('name')->label('Name'),
            SlugField::make('code')->label('Code'),
            BelongsToManyField::make('tags')->label('Tags')->model(Brand::class),
            BelongsToField::make('brand_id')->label('Brand')->model(Brand::class),
            BelongsToField::make('maker_id')->label('Maker')->model(Brand::class)->valueColumn('xml_id'),
            BelongsToField::make('keeper')->label('Keeper')->model(Brand::class)->valueColumn('xml_id'),
            BelongsToManyField::make('owner')->label('Owner')->model(Brand::class),
            BelongsToManyField::make('parts')->label('Parts')->model(Brand::class),
        ];
    }
}
