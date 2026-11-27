<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Builder;

use Atrium\Admin\Fields\LinkField;
use Atrium\Admin\Support\RecordUrls;
use Atrium\Theme\Pages;

/**
 * Where the value of a LinkField leads, for a visitor.
 */
final class BlockLinks
{
    public function __construct(
        private readonly Pages $pages,
        private readonly RecordUrls $urls,
    ) {}

    /**
     * The address, or null: no link, an address a browser must not follow, a
     * record that is gone, hidden or not written in this language.
     */
    public function url(mixed $link): ?string
    {
        if (! is_array($link)) {
            return null;
        }

        if (($link['type'] ?? null) === LinkField::URL) {
            $url = is_string($link['url'] ?? null) ? trim($link['url']) : '';

            return LinkField::isSafeUrl($url) ? $url : null;
        }

        $model = $link['model'] ?? null;
        $id = $link['id'] ?? null;

        // Only a model the picker offers: the tree is data, not a class name
        // to run.
        if (($link['type'] ?? null) !== LinkField::RECORD || ! is_string($model) || ! is_numeric($id) || ! array_key_exists($model, LinkField::make('link')->targets())) {
            return null;
        }

        $record = $this->pages->visible($model)->whereKey((int) $id)->first();

        return $record === null ? null : $this->urls->url($record);
    }
}
