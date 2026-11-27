<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures;

use Atrium\Admin\Builder\BlockType;
use Closure;

/** A block type a test shapes as it needs, and that remembers its settings. */
final class ProbeBlock extends BlockType
{
    /** @var array<int,array<array-key,mixed>> what data() was given, in order */
    public array $given = [];

    /**
     * @param  array<int,string>  $styles  file names in Fixtures/builder
     * @param  array<int,string>  $scripts
     * @param  array<int,string>  $translatable
     */
    public function __construct(
        private readonly string $view = 'probe::text',
        private readonly array $styles = [],
        private readonly array $scripts = [],
        private readonly array $translatable = [],
        private readonly ?Closure $data = null,
    ) {}

    public function label(): string
    {
        return 'Probe';
    }

    public function view(): string
    {
        return $this->view;
    }

    public function translatable(): array
    {
        return $this->translatable;
    }

    public function data(array $settings): ?array
    {
        $this->given[] = $settings;

        return $this->data === null ? ['text' => $settings['text'] ?? ''] : ($this->data)($settings);
    }

    public function styles(): array
    {
        return array_map(self::path(...), $this->styles);
    }

    public function scripts(): array
    {
        return array_map(self::path(...), $this->scripts);
    }

    public static function path(string $file): string
    {
        return __DIR__.'/builder/'.$file;
    }
}
