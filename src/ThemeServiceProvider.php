<?php

declare(strict_types=1);

namespace Atrium\Theme;

use Illuminate\Support\ServiceProvider;

final class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/atrium-theme.php', 'atrium-theme');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/atrium-theme.php' => config_path('atrium-theme.php'),
            ], 'atrium-theme-config');
        }
    }
}
