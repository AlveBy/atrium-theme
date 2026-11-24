<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures;

use Atrium\Admin\Embeds\Embed;
use Atrium\Admin\Models\AdminUser;

/** An embed that draws "NOTE <id>" and needs no table. */
final class NoteEmbed implements Embed
{
    public function label(): string
    {
        return 'Note';
    }

    public function available(): bool
    {
        return true;
    }

    public function permitted(?AdminUser $user): bool
    {
        return true;
    }

    public function options(string $search): array
    {
        return [];
    }

    public function labels(array $ids): array
    {
        return [];
    }

    public function render(string $id): string
    {
        return '<aside>NOTE '.$id.'</aside>';
    }
}
