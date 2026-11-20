<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures\Home;

use Illuminate\Database\Eloquent\Model;

/**
 * Stands in for the Pages module's tree: the address of a page by its code.
 */
class PageTree
{
    public function url(Model $page): string
    {
        return 'https://example.test/pages/'.$page->getAttribute('code');
    }
}
