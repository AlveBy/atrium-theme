<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme;

use Atrium\Admin\Support\ContentLanguages;
use Atrium\Admin\Support\PublicUrl;
use Atrium\Theme\Skins\Skins;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * The modules that have pages on the site, read from
 * config('atrium.public_urls'), and the way to their records: which ones
 * are shown, in what order, at what address.
 *
 * A section model is the one whose `tree` is itself. Several models may
 * share a prefix: a catalog's sections and products both live under
 * /catalog/.
 */
final class Pages
{
    /** How many records one code may match before the lenient lookup gives up. */
    private const MAX_CANDIDATES = 50;

    /** @var array<string,array<string,true>> */
    private array $columns = [];

    /**
     * Prefix => [model class => rule], the longest prefix first, so that
     * `catalog/tours` is tried before `catalog`.
     *
     * @return array<string,array<class-string<Model>,array{prefix:string,parent:?string,tree:?string,parent_column:string}>>
     */
    public function modules(): array
    {
        $modules = [];

        foreach (array_keys((array) config('atrium.public_urls', [])) as $class) {
            $rule = PublicUrl::ruleFor((string) $class);

            if ($rule === null || $rule['prefix'] === '' || ! is_subclass_of((string) $class, Model::class)) {
                continue;
            }

            $modules[$rule['prefix']][$class] = $rule;
        }

        uksort($modules, static fn (string $a, string $b): int => strlen($b) <=> strlen($a) ?: strcmp($a, $b));

        return $modules;
    }

    /**
     * @return array<int,string>
     */
    public function prefixes(): array
    {
        return array_keys($this->modules());
    }

    /**
     * @return array<class-string<Model>,array{prefix:string,parent:?string,tree:?string,parent_column:string}>
     */
    public function module(string $prefix): array
    {
        return $this->modules()[$prefix] ?? [];
    }

    /**
     * @param  array{tree:?string}  $rule
     */
    public function isSectionModel(string $class, array $rule): bool
    {
        return $rule['tree'] === $class;
    }

    /**
     * The models of a prefix that hold the pages themselves, not sections.
     *
     * @return array<int,class-string<Model>>
     */
    public function elementModels(string $prefix): array
    {
        return array_keys(array_filter(
            $this->module($prefix),
            fn (array $rule, string $class): bool => ! $this->isSectionModel($class, $rule),
            ARRAY_FILTER_USE_BOTH,
        ));
    }

    /**
     * @return array<int,class-string<Model>>
     */
    public function sectionModels(string $prefix): array
    {
        return array_keys(array_filter(
            $this->module($prefix),
            fn (array $rule, string $class): bool => $this->isSectionModel($class, $rule),
            ARRAY_FILTER_USE_BOTH,
        ));
    }

    /**
     * The view that draws a module's page: its own when the model names one
     * (`public static function themeView(string $page): ?string`, where
     * `$page` is index, section or page), the theme's otherwise.
     */
    public function view(string $class, string $page, string $default): string
    {
        $view = method_exists($class, 'themeView') ? $class::themeView($page) : null;

        return is_string($view) && view()->exists($view) ? $view : $default;
    }

    public function label(string $class): string
    {
        // A skin names the modules for its kind of site: the catalog of a
        // travel agency is its tours.
        $skin = app(Skins::class)->current();
        $prefix = PublicUrl::ruleFor($class)['prefix'] ?? null;

        if ($skin !== null && $prefix !== null && $skin->hasText($key = "modules.{$prefix}")) {
            return $skin->text($key);
        }

        return method_exists($class, 'getAdminLabel')
            ? (string) $class::getAdminLabel()
            : Str::headline(Str::plural(class_basename($class)));
    }

    /**
     * What a visitor may see, in the order editors set: active, inside its
     * active period, by sort, the newest first among equals. The id comes
     * last, or records with the same sort swap places between pages.
     *
     * @param  class-string<Model>  $class
     * @return Builder<Model>
     */
    public function visible(string $class): Builder
    {
        $query = $class::query();
        $has = $this->columns($query->getModel());

        if (isset($has['active'])) {
            $query->where('active', true);
        }

        if (isset($has['active_from'])) {
            $query->where(fn (Builder $q) => $q->whereNull('active_from')->orWhere('active_from', '<=', now()));
        }

        if (isset($has['active_to'])) {
            $query->where(fn (Builder $q) => $q->whereNull('active_to')->orWhere('active_to', '>=', now()));
        }

        if (isset($has['sort'])) {
            $query->orderBy('sort');
        }

        if (isset($has['active_from'])) {
            $query->orderByDesc('active_from');
        }

        return $query->orderBy($query->getModel()->getKeyName());
    }

    /**
     * Find a page by its address under a prefix.
     *
     * The exact address wins. Failing that, a record with the same code
     * anywhere under the prefix is returned, and the controller redirects
     * to its real address: a record moved to another section, or one
     * reached by an old link without the section, must not end in a 404.
     */
    public function find(string $prefix, string $path): ?Model
    {
        $path = trim($path, '/');
        $code = Str::afterLast($path, '/');
        $fallback = null;

        foreach (array_keys($this->module($prefix)) as $class) {
            $query = $this->visible($class);
            $model = $query->getModel();
            $column = $this->isTranslatable($model, 'code') ? 'code->'.app()->getLocale() : 'code';

            foreach ($query->where($column, $code)->limit(self::MAX_CANDIDATES)->get() as $record) {
                if ($this->path($record) === $path) {
                    return $record;
                }

                $fallback ??= $record;
            }
        }

        return $fallback;
    }

    /**
     * The address of a record below its prefix: section path and code.
     */
    public function path(Model $record, ?string $language = null): ?string
    {
        $rule = PublicUrl::ruleFor($record::class);
        $code = $this->text($record, 'code', $language);

        if ($rule === null || $code === '') {
            return null;
        }

        $parent = $rule['parent'] === null || $rule['tree'] === null
            ? ''
            : (PublicUrl::sectionPaths($rule['tree'], $rule['parent_column'])[$record->getAttribute($rule['parent'])] ?? '');

        return ltrim($parent.'/'.$code, '/');
    }

    /**
     * The record's page in a language, or null when it has none there: a
     * translated record needs its name and code in that language.
     */
    public function url(Model $record, ?string $language = null): ?string
    {
        $language ??= app()->getLocale();
        $rule = PublicUrl::ruleFor($record::class);

        if ($rule === null || ($this->isTranslatable($record, 'name') && $this->text($record, 'name', $language) === '')) {
            return null;
        }

        $path = $this->path($record, $language);

        return $path === null ? null : app(ContentLanguages::class)->route('atrium.theme.page', [
            'prefix' => $rule['prefix'],
            'path' => $path,
        ], $language);
    }

    /**
     * The record's page in every language it is written in. A record that
     * is not translated has one text, so one page: the current language's.
     *
     * @return array<string,string>
     */
    public function urls(Model $record): array
    {
        $languages = $this->isTranslatable($record, 'code')
            ? app(ContentLanguages::class)->codes()
            : [app()->getLocale()];

        $urls = [];

        foreach ($languages as $language) {
            if (($url = $this->url($record, $language)) !== null) {
                $urls[$language] = $url;
            }
        }

        return $urls;
    }

    public function indexUrl(string $prefix, ?string $language = null): string
    {
        return app(ContentLanguages::class)->route('atrium.theme.index', ['prefix' => $prefix], $language);
    }

    public function homeUrl(?string $language = null): string
    {
        // The app may have a home page of its own.
        if (! Route::has('atrium.theme.home')) {
            return url('/');
        }

        return app(ContentLanguages::class)->route('atrium.theme.home', [], $language);
    }

    /**
     * A text attribute in a language, without falling back to another one.
     */
    public function text(Model $record, string $attribute, ?string $language = null): string
    {
        if ($this->isTranslatable($record, $attribute)) {
            /** @phpstan-ignore method.notFound */
            return (string) $record->getTranslation($attribute, $language ?? app()->getLocale(), false);
        }

        $value = $record->getAttribute($attribute);

        return is_scalar($value) ? (string) $value : '';
    }

    public function isTranslatable(Model $model, string $attribute): bool
    {
        return method_exists($model, 'isTranslatableAttribute') && $model->isTranslatableAttribute($attribute);
    }

    /**
     * @return array<string,true>
     */
    public function columns(Model $model): array
    {
        $table = implode('|', [$model->getConnectionName(), $model->getTable()]);

        return $this->columns[$table] ??= array_fill_keys(
            Schema::connection($model->getConnectionName())->getColumnListing($model->getTable()),
            true,
        );
    }
}
