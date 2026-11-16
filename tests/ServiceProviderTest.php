<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

final class ServiceProviderTest extends TestCase
{
    public function test_config_is_merged(): void
    {
        $this->assertIsArray(config('atrium-theme'));
    }
}
