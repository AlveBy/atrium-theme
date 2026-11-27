<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures;

use Atrium\Admin\Contracts\Administrable;
use Atrium\Admin\Traits\InteractsWithAdmin;

/** A post as a module of the panel: what a link field offers to pick. */
final class LinkedStory extends Post implements Administrable
{
    use InteractsWithAdmin;

    protected $table = 'posts';

    public static function getAdministrationListColumns(): array
    {
        return [];
    }
}
