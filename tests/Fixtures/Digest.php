<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures;

use Atrium\Admin\Contracts\Sitemapable;
use Illuminate\Database\Eloquent\Builder;

/**
 * A module that lists itself in the sitemap.
 */
class Digest extends Post implements Sitemapable
{
    protected $table = 'posts';

    public static function sitemapQuery(): Builder
    {
        return static::query();
    }

    public function sitemapUrl(string $language): ?string
    {
        return null;
    }
}
