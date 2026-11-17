<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures;

use Atrium\Admin\Fields\NumberField;
use Atrium\Admin\Modules\ModuleSettings;

final class ShopSettings extends ModuleSettings
{
    public function fields(): array
    {
        return [NumberField::make('per_page')->label('Records a page')];
    }
}
