<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Builder\Blocks;

use Atrium\Admin\Fields\MapField;
use Atrium\Admin\Fields\NumberField;
use Atrium\Admin\Fields\TextField;
use Closure;

/**
 * A place on OpenStreetMap, with the address under it and a link to the full
 * map. The frame is loaded from openstreetmap.org when it comes into view.
 */
final class MapBlock extends ThemeBlock
{
    private const ZOOM = 15;

    private const MAX_ZOOM = 19;

    public function label(): string
    {
        return __('atrium-theme::builder.types.map');
    }

    public function fields(): array
    {
        return [
            MapField::make('point')
                ->label(__('atrium-theme::builder.fields.point'))
                ->required()
                ->rules([function (string $attribute, mixed $value, Closure $fail): void {
                    if ($this->point($value) === null) {
                        $fail(__('atrium-theme::builder.map.invalid_point'));
                    }
                }]),
            NumberField::make('zoom')
                ->label(__('atrium-theme::builder.fields.zoom'))
                ->min(0)
                ->max(self::MAX_ZOOM)
                ->rules('integer')
                ->default(self::ZOOM),
            TextField::make('address')->label(__('atrium-theme::builder.fields.address'))->maxLength(255),
        ];
    }

    public function translatable(): array
    {
        return ['address'];
    }

    public function view(): string
    {
        return 'atrium-theme::builder.blocks.map';
    }

    public function data(array $settings): ?array
    {
        $point = $this->point($settings['point'] ?? null);

        if ($point === null) {
            return null;
        }

        [$latitude, $longitude] = $point;
        $zoom = $settings['zoom'] ?? null;
        $zoom = is_numeric($zoom) ? max(0, min(self::MAX_ZOOM, (int) $zoom)) : self::ZOOM;

        // Two tiles across at this zoom, in a frame twice as wide as high.
        $width = 720 / 2 ** $zoom;
        $height = $width / 2 * cos(deg2rad($latitude));
        $box = [
            max(-180.0, $longitude - $width / 2),
            max(-90.0, $latitude - $height / 2),
            min(180.0, $longitude + $width / 2),
            min(90.0, $latitude + $height / 2),
        ];
        $marker = $this->number($latitude).','.$this->number($longitude);

        return [
            'src' => 'https://www.openstreetmap.org/export/embed.html?'.http_build_query([
                'bbox' => implode(',', array_map($this->number(...), $box)),
                'layer' => 'mapnik',
                'marker' => $marker,
            ]),
            'href' => 'https://www.openstreetmap.org/?'.http_build_query([
                'mlat' => $this->number($latitude),
                'mlon' => $this->number($longitude),
            ])."#map={$zoom}/{$this->number($latitude)}/{$this->number($longitude)}",
            'address' => $this->text($settings, 'address'),
        ];
    }

    public function styles(): array
    {
        return [$this->css('map')];
    }

    /**
     * "48.8584, 2.2945" as latitude and longitude, or null.
     *
     * @return array{0:float,1:float}|null
     */
    private function point(mixed $value): ?array
    {
        if (! is_string($value) || preg_match('/^\s*(-?\d{1,2}(?:\.\d+)?)\s*,\s*(-?\d{1,3}(?:\.\d+)?)\s*$/', $value, $match) !== 1) {
            return null;
        }

        [$latitude, $longitude] = array_map(floatval(...), array_slice($match, 1));

        return abs($latitude) <= 90 && abs($longitude) <= 180 ? [$latitude, $longitude] : null;
    }

    /** Never in the exponent notation PHP prints small numbers in. */
    private function number(float $value): string
    {
        return number_format($value, 6, '.', '');
    }
}
