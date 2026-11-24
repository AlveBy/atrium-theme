<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme;

use Atrium\Admin\Cache\Card;
use Atrium\Admin\Cache\RecordCache;
use Atrium\Admin\Contracts\Cacheable;
use Atrium\Admin\Fields\BelongsToField;
use Atrium\Admin\Support\ContentLanguages;
use Atrium\Admin\Support\PublicUrl;
use Atrium\Admin\Templates\Templates;
use Atrium\Theme\Skins\Skins;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use WeakMap;

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

    /** @var array<class-string<Model>,array<string,array<int|string,Model|null>>> linked records by model, column and value, for preloadLinks() */
    private array $links = [];

    /** @var array<string,array<string,true>> */
    private array $columns = [];

    /** @var WeakMap<Model,Card>|null the card each model from the cache came from */
    private ?WeakMap $cards = null;

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
     * `$page` is index, section or page), then the skin's, the theme's
     * otherwise.
     */
    public function view(string $class, string $page, string $default): string
    {
        $view = method_exists($class, 'themeView') ? $class::themeView($page) : null;

        if (is_string($view) && view()->exists($view)) {
            return $view;
        }

        // A skin may draw the theme's pages its own way: views "list" (a
        // module's list and a section) and "page" (a record).
        $skin = app(Skins::class)->current();
        $own = $page === 'page' ? 'page' : 'list';

        return $skin !== null && $skin->hasView($own) ? $skin->view($own) : $default;
    }

    /**
     * The view that draws a record: the template its editor picked, or
     * $default, the view of view() when it has none or the file is gone.
     */
    public function template(Model $record, string $default): string
    {
        return app(Templates::class)->viewOf($record, $default);
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

        // A module may give visitors a name other than the admin's: the
        // admin keeps "FAQ sections" apart from "FAQ questions", a visitor
        // reads "FAQ".
        foreach (['getPublicLabel', 'getAdminLabel'] as $method) {
            if (method_exists($class, $method)) {
                return (string) $class::$method();
            }
        }

        return Str::headline(Str::plural(class_basename($class)));
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
        // Time in steps, not to the second: the cached lists keep their keys.
        $now = app(RecordCache::class)->now();

        if (isset($has['active'])) {
            $query->where('active', true);
        }

        if (isset($has['active_from'])) {
            $query->where(fn (Builder $q) => $q->whereNull('active_from')->orWhere('active_from', '<=', $now));
        }

        if (isset($has['active_to'])) {
            $query->where(fn (Builder $q) => $q->whereNull('active_to')->orWhere('active_to', '>=', $now));
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
            $query->where($column, $code)->limit(self::MAX_CANDIDATES);

            if ($this->isCached($class)) {
                foreach ($query->cachedCards() as $card) {
                    $record = $this->remember($card);

                    if ($this->page($record)->path() === $path) {
                        return $record;
                    }

                    $fallback ??= $record;
                }

                continue;
            }

            foreach ($query->get() as $record) {
                if ($this->path($record) === $path) {
                    return $record;
                }

                $fallback ??= $record;
            }
        }

        return $fallback;
    }

    /**
     * Whether a module's records come from the record cache.
     */
    public function isCached(string $class): bool
    {
        return is_subclass_of($class, Cacheable::class) && app(RecordCache::class)->enabled();
    }

    /**
     * Records of a query as pages: from their cards when the model is
     * cached (one cache read, no query on a warm cache), from the
     * database otherwise.
     *
     * @param  Builder<Model>  $query
     * @return array<int,RecordView>
     */
    public function records(Builder $query): array
    {
        $class = $query->getModel()::class;

        if (! $this->isCached($class)) {
            return array_map(fn (Model $record): RecordView => new RecordView($record, $this), $query->get()->all());
        }

        return array_map(fn (Card $card): RecordView => RecordView::fromCard($card, $this), $query->cachedCards());
    }

    /**
     * One record as a page, by id.
     *
     * @param  class-string<Model>  $class
     */
    public function record(string $class, int|string $id): ?RecordView
    {
        if ($this->isCached($class)) {
            $card = app(RecordCache::class)->cards($class, [$id])[0] ?? null;

            return $card === null ? null : RecordView::fromCard($card, $this);
        }

        $record = $class::query()->find($id);

        return $record instanceof Model ? new RecordView($record, $this) : null;
    }

    /**
     * The page of a record: from its card when it came from the cache.
     */
    public function page(Model $record): RecordView
    {
        $card = $this->cards?->offsetExists($record) ? $this->cards[$record] : null;

        return $card instanceof Card ? RecordView::fromCard($card, $this) : new RecordView($record, $this);
    }

    private function remember(Card $card): Model
    {
        $this->cards ??= new WeakMap;
        $model = $card->model();
        $this->cards[$model] = $card;

        return $model;
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
     * Load the records a batch links to through its form's belongs-to
     * fields and belongs-to-many relations, a query per field instead of
     * one per record.
     *
     * @param  EloquentCollection<int,Model>  $records
     */
    public function preloadLinks(EloquentCollection $records): void
    {
        $first = $records->first();

        // Read like RecordView reads them: a model needs only the fields.
        if ($first === null || ! method_exists($class = $first::class, 'getAdministrationFields')) {
            return;
        }

        $relations = [];

        foreach ($class::getAdministrationFields() as $field) {
            if (! $field instanceof BelongsToField || ($class = $field->getModelClass()) === null) {
                continue;
            }

            if ($field->getType() === 'belongsToMany') {
                if (method_exists($first, $field->getName()) && $first->{$field->getName()}() instanceof BelongsToMany) {
                    $relations[] = $field->getName();
                }

                continue;
            }

            $values = $records->map(fn (Model $record): mixed => $record->getAttribute($field->getColumn()))
                ->filter(fn (mixed $value): bool => is_scalar($value) && $value !== '')
                ->unique()
                ->reject(fn (mixed $value): bool => array_key_exists($value, $this->links[$class][$field->getValueColumn()] ?? []))
                ->values()
                ->all();

            if ($values === []) {
                continue;
            }

            foreach ($values as $value) {
                $this->links[$class][$field->getValueColumn()][$value] = null;
            }

            /** @var class-string<Model> $modelClass */
            $modelClass = $class;

            foreach ($modelClass::query()->whereIn($field->getValueColumn(), $values)->get() as $related) {
                $this->links[$class][$field->getValueColumn()][$related->getAttribute($field->getValueColumn())] = $related;
            }
        }

        if ($relations !== []) {
            $records->loadMissing($relations);
        }
    }

    /**
     * The record a belongs-to field points at: from preloadLinks() while
     * its batch is being drawn, a query otherwise.
     *
     * @param  class-string<Model>  $class
     */
    public function linked(string $class, string $column, mixed $value): ?Model
    {
        $links = $this->links[$class][$column] ?? [];

        return array_key_exists($value, $links) ? $links[$value] : $class::query()->where($column, $value)->first();
    }

    /**
     * Drop what preloadLinks() loaded: it holds for one batch only.
     */
    public function forgetLinks(): void
    {
        $this->links = [];
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
