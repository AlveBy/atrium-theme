<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures\Home;

use Illuminate\Database\Eloquent\Model;

/**
 * Stands in for a question of the Faq module. Its table is never read: the
 * questions come from the Faq stand-in.
 */
class Question extends Model {}
