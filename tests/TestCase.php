<?php

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Theme\ThemeServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [ThemeServiceProvider::class];
    }
}
