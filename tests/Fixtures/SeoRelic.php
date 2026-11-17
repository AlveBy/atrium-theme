<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures;

/**
 * A relic with the hooks of a module that keeps its own SEO texts.
 */
class SeoRelic extends Relic
{
    public static mixed $title = null;

    public static mixed $description = null;

    public static ?string $keywords = null;

    public static mixed $image = null;

    public function getSeoTitle(): mixed
    {
        return static::$title;
    }

    public function getSeoDescription(): mixed
    {
        return static::$description;
    }

    public function getSeoKeywords(): ?string
    {
        return static::$keywords;
    }

    public function getOgImage(): mixed
    {
        return static::$image;
    }
}
