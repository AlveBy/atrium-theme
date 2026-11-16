<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures;

use Atrium\Admin\Fields\BelongsToField;
use Atrium\Admin\Fields\BelongsToManyField;
use Atrium\Admin\Fields\CheckboxField;
use Atrium\Admin\Fields\ContentField;
use Atrium\Admin\Fields\DateTimeField;
use Atrium\Admin\Fields\FileField;
use Atrium\Admin\Fields\GalleryField;
use Atrium\Admin\Fields\ImageField;
use Atrium\Admin\Fields\NumberField;
use Atrium\Admin\Fields\SelectField;
use Atrium\Admin\Fields\SlugField;
use Atrium\Admin\Fields\TextField;
use Atrium\Admin\Fields\TreeSelectField;
use Atrium\Admin\Traits\HasAdminSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Shaped like a model the Bitrix migrator generates.
 */
class Product extends Model
{
    use HasAdminSeo;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['active' => 'boolean', 'active_from' => 'datetime', 'active_to' => 'datetime'];
    }

    public static function getAdminLabel(): string
    {
        return 'Products';
    }

    public function related(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'products_related', 'product_id', 'related_id');
    }

    public static function getAdministrationFields(): array
    {
        return [
            CheckboxField::make('active')->label('Active'),
            DateTimeField::make('active_from')->label('Active from'),
            TextField::make('name')->label('Name'),
            SlugField::make('code')->label('Code'),
            TreeSelectField::make('section_id')->label('Section')->model(ProductSection::class),
            TextField::make('article')->label('Article'),
            NumberField::make('price')->label('Price'),
            SelectField::make('color')->label('Color')->options(['silver' => 'Silver', 'black' => 'Black']),
            BelongsToField::make('brand')->label('Brand')->model(Brand::class),
            BelongsToManyField::make('related')->label('Related')->model(self::class),
            CheckboxField::make('orderable')->label('Orderable')->checkedValue('Y'),
            ImageField::make('preview_picture')->label('Preview picture')->tab('preview'),
            ContentField::make('preview_text')->label('Preview text')->tab('preview'),
            ImageField::make('detail_picture')->label('Detail picture')->tab('detail'),
            ContentField::make('detail_text')->label('Detail text')->tab('detail'),
            ContentField::make('specs')->label('Specifications')->tab('detail'),
            GalleryField::make('photos')->label('Photos')->tab('detail'),
            FileField::make('manual')->label('Manual')->tab('detail'),
            TextField::make('meta_title')->label('Meta title')->tab('seo'),
        ];
    }
}
