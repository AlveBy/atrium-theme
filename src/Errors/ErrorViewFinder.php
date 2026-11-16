<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Errors;

use Illuminate\Foundation\Exceptions\RegisterErrorViewPaths;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use InvalidArgumentException;

/**
 * Laravel's view finder with one more stop for error pages.
 *
 * Laravel draws an HTTP error with the view errors::404 (then errors::4xx),
 * looked up in resources/views/errors of the app and then in the framework.
 * The theme's pages go in between, so the order is:
 *
 *   1. the app's resources/views/errors/{site code}/404, then …/4xx;
 *   2. the app's resources/views/errors/404, then 4xx;
 *   3. the theme's atrium-theme::errors.404, then 4xx (not in the admin);
 *   4. Laravel's own page.
 *
 * Laravel registers the errors namespace anew before every error page, so
 * the order cannot live in the namespace itself; the finder applies it to
 * every errors::{code} it is asked for, which also covers
 * `php artisan down --render=errors::503`.
 */
final class ErrorViewFinder extends FileViewFinder
{
    private ErrorPages $pages;

    private string $framework;

    /**
     * Puts the finder in place of Laravel's own. A finder some other package
     * has already replaced is left as it is.
     */
    public static function install(Factory $view, ErrorPages $pages): void
    {
        $finder = $view->getFinder();

        if ($finder::class !== FileViewFinder::class) {
            return;
        }

        $self = new self($finder->files, $finder->paths, $finder->extensions);
        $self->hints = $finder->hints;
        $self->views = $finder->views;
        $self->pages = $pages;
        $self->framework = (string) realpath(dirname((string) (new \ReflectionClass(RegisterErrorViewPaths::class))->getFileName()).'/views');

        $view->setFinder($self);
    }

    /**
     * The errors namespace changes before every error page: what was found
     * for the previous request (another site, the admin) is forgotten.
     *
     * @param  string  $namespace
     * @param  string|array<int,string>  $hints
     */
    public function replaceNamespace($namespace, $hints): void
    {
        parent::replaceNamespace($namespace, $hints);

        if ($namespace === 'errors') {
            $this->views = array_filter($this->views, static fn (string $name): bool => ! str_starts_with($name, 'errors::'), ARRAY_FILTER_USE_KEY);
        }
    }

    /**
     * @param  string  $name
     */
    protected function findNamespacedView($name): string
    {
        [$namespace, $code] = $this->parseNamespaceSegments($name);

        if ($namespace !== 'errors' || preg_match('/^[1-5]\d\d$/', $code) !== 1) {
            return parent::findNamespacedView($name);
        }

        $group = $code[0].'xx';
        $app = array_values(array_filter($this->hints['errors'], fn (string $path): bool => $path !== $this->framework));
        $site = $this->pages->siteCode();

        $candidates = preg_match('/^[A-Za-z0-9_-]+$/', $site) === 1
            ? [[$site.'.'.$code, $app], [$site.'.'.$group, $app]]
            : [];

        array_push($candidates, [$code, $app], [$group, $app]);

        if ($this->pages->themed()) {
            $theme = $this->hints['atrium-theme'] ?? [];
            array_push($candidates, ['errors.'.$code, $theme], ['errors.'.$group, $theme]);
        }

        foreach ($candidates as [$view, $paths]) {
            try {
                return $this->findInPaths($view, $paths);
            } catch (InvalidArgumentException) {
                // Not there: the next place.
            }
        }

        return parent::findNamespacedView($name);
    }
}
