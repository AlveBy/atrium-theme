<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures\Home;

use Illuminate\Support\Collection;

/**
 * Stands in for the Faq service: questions with gaps in their keys, as a
 * filtered list has them.
 */
class Faq
{
    /**
     * @return Collection<int,Question>
     */
    public function questions(): Collection
    {
        return collect([
            (new Question)->forceFill(['id' => 1]),
            (new Question)->forceFill(['id' => 2]),
            (new Question)->forceFill(['id' => 3]),
        ])->filter(fn (Question $question): bool => $question->getAttribute('id') !== 1);
    }
}
