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
use Atrium\Admin\Fields\CheckboxField;
use Atrium\Admin\Fields\ContentField;
use Atrium\Admin\Fields\GalleryField;
use Atrium\Admin\Fields\ImageField;
use Atrium\Admin\Fields\NumberField;
use Atrium\Admin\Fields\SelectField;
use Atrium\Admin\Fields\SlugField;
use Atrium\Admin\Fields\TextField;
use Atrium\Admin\Traits\HasAdminSeo;
use Atrium\Admin\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * The product of the cache benchmark: a translated name, code and text,
 * 40 properties of every kind, a brand, related products, pictures and SEO.
 */
class BenchProduct extends Model implements Cacheable
{
    use CachesRecords, HasAdminSeo, Translatable;

    /** Properties of each kind: 16 lines, 10 numbers, 6 lists, 4 flags, 2 texts, 2 links. */
    public const TEXTS = 16;

    public const NUMBERS = 10;

    public const LISTS = 6;

    public const FLAGS = 4;

    public const CONTENTS = 2;

    protected $table = 'bench_products';

    protected $guarded = [];

    /** @var array<int,string> */
    protected array $translatable = ['name', 'code', 'detail_text'];

    protected function casts(): array
    {
        return ['active' => 'boolean', 'active_from' => 'datetime', 'active_to' => 'datetime'];
    }

    public static function getAdminLabel(): string
    {
        return 'Benchmark products';
    }

    /**
     * @return BelongsToMany<self,$this>
     */
    public function related(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'bench_related', 'product_id', 'related_id');
    }

    /**
     * The 40 property columns: name => kind.
     *
     * @return array<string,string>
     */
    public static function properties(): array
    {
        $columns = [];

        foreach (['text' => self::TEXTS, 'number' => self::NUMBERS, 'list' => self::LISTS, 'flag' => self::FLAGS, 'content' => self::CONTENTS] as $kind => $count) {
            for ($i = 1; $i <= $count; $i++) {
                $columns[$kind.'_'.$i] = $kind;
            }
        }

        return $columns + ['brand_id' => 'brand', 'maker_id' => 'brand'];
    }

    public static function getAdministrationFields(): array
    {
        $fields = [
            CheckboxField::make('active')->label('Active'),
            TextField::make('name')->label('Name'),
            SlugField::make('code')->label('Code'),
        ];

        foreach (self::properties() as $column => $kind) {
            $label = ucfirst(str_replace('_', ' ', $column));
            $fields[] = match ($kind) {
                'text' => TextField::make($column)->label($label),
                'number' => NumberField::make($column)->label($label),
                'list' => SelectField::make($column)->label($label)->options(['a' => 'Option A', 'b' => 'Option B', 'c' => 'Option C']),
                'flag' => CheckboxField::make($column)->label($label)->checkedValue('Y'),
                'content' => ContentField::make($column)->label($label)->tab('detail'),
                default => BelongsToField::make($column)->label($label)->model(Brand::class),
            };
        }

        return [
            ...$fields,
            BelongsToManyField::make('related')->label('Related')->model(self::class),
            ImageField::make('preview_picture')->label('Preview picture')->tab('preview'),
            ContentField::make('preview_text')->label('Preview text')->tab('preview'),
            ImageField::make('detail_picture')->label('Detail picture')->tab('detail'),
            ContentField::make('detail_text')->label('Detail text')->tab('detail'),
            GalleryField::make('photos')->label('Photos')->tab('detail'),
            TextField::make('meta_title')->label('Meta title')->tab('seo'),
        ];
    }
}
