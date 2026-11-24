<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Http\Controllers;

use Atrium\Admin\Cache\Card;
use Atrium\Admin\Facades\Setting;
use Atrium\Admin\Modules\Modules;
use Atrium\Admin\Support\ContentLanguages;
use Atrium\Admin\Support\PublicUrl;
use Atrium\Theme\Pages;
use Atrium\Theme\RecordView;
use Atrium\Theme\Skins\HomeContent;
use Atrium\Theme\Skins\Skins;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Every public page of the theme: the home page, a module's list, a
 * section with what is in it, and a record.
 */
final class PageController extends Controller
{
    /** How many section levels the breadcrumbs climb at most. */
    private const MAX_DEPTH = 20;

    public function __construct(
        private readonly Pages $pages,
        private readonly ContentLanguages $languages,
    ) {}

    public function home(Skins $skins, HomeContent $content): View
    {
        $switch = $this->everyLanguage(fn (string $code): string => $this->pages->homeUrl($code));

        // A skin draws its home page from the modules itself.
        if (($skin = $skins->current()) !== null) {
            return view($skin->view('home'), [
                'home' => $content,
                'canonical' => $this->pages->homeUrl(),
                'alternates' => $switch,
                'switch' => $switch,
            ]);
        }

        $modules = [];

        foreach ($this->pages->prefixes() as $prefix) {
            $class = $this->pages->elementModels($prefix)[0] ?? $this->pages->sectionModels($prefix)[0];

            $modules[] = [
                'label' => $this->pages->label($class),
                'url' => $this->pages->indexUrl($prefix),
                'records' => $this->withUrls($this->pages->records($this->pages->visible($class)->limit((int) config('atrium-theme.home_items', 3)))),
            ];
        }

        return view('atrium-theme::home', [
            'modules' => $modules,
            'canonical' => $this->pages->homeUrl(),
            'alternates' => $switch,
            'switch' => $switch,
        ]);
    }

    public function index(Request $request, string $prefix): View
    {
        $sections = [];

        foreach ($this->pages->sectionModels($prefix) as $class) {
            $rule = $this->pages->module($prefix)[$class];
            $sections = [...$sections, ...$this->withUrls($this->pages->records($this->pages->visible($class)->whereNull($rule['parent_column'])))];
        }

        $class = $this->pages->elementModels($prefix)[0] ?? null;
        $records = $class === null ? null : $this->paginate($this->pages->visible($class));
        $switch = $this->everyLanguage(fn (string $code): string => $this->pages->indexUrl($prefix, $code));

        return view($this->pages->view($class ?? $this->pages->sectionModels($prefix)[0], 'index', 'atrium-theme::list'), [
            'title' => $this->pages->label($class ?? $this->pages->sectionModels($prefix)[0]),
            'text' => null,
            'sections' => $sections,
            'records' => $records,
            'canonical' => $this->canonical($request, $this->pages->indexUrl($prefix)),
            'breadcrumbs' => [],
            'alternates' => $request->query('page') ? [] : $switch,
            'switch' => $switch,
        ]);
    }

    public function show(Request $request, string $prefix, string $path): View|RedirectResponse
    {
        $record = $this->pages->find($prefix, $path);

        abort_if($record === null, 404);

        // From its card when the module is cached: no query.
        $view = $this->pages->page($record);
        $url = $view->url();

        abort_if($url === null, 404);

        // Found by its code under another path: send the visitor and search
        // engines to where it lives now.
        if ($view->path() !== trim($path, '/')) {
            return redirect()->to($url, 301);
        }

        $rule = $this->pages->module($prefix)[$record::class];
        $urls = $view->urls();
        $switch = $this->everyLanguage(fn (string $code): string => $urls[$code] ?? $this->pages->homeUrl($code));
        $breadcrumbs = $this->breadcrumbs($prefix, $record, $rule);

        if ($this->pages->isSectionModel($record::class, $rule)) {
            return $this->section($request, $prefix, $record, $view, $url, $breadcrumbs, $urls, $switch);
        }

        $default = $this->pages->view($record::class, 'page', 'atrium-theme::page');

        return view($this->pages->template($record, $default), [
            'defaultView' => $default,
            'page' => $view,
            'canonical' => $url,
            'breadcrumbs' => $breadcrumbs,
            'alternates' => $urls,
            'switch' => $switch,
        ]);
    }

    /**
     * @param  array<int,array{label:string,url:string}>  $breadcrumbs
     * @param  array<string,string>  $urls
     * @param  array<string,string>  $switch
     */
    private function section(Request $request, string $prefix, Model $section, RecordView $view, string $url, array $breadcrumbs, array $urls, array $switch): View
    {
        $rule = $this->pages->module($prefix)[$section::class];
        $children = $this->withUrls($this->pages->records($this->pages->visible($section::class)->where($rule['parent_column'], $section->getKey())));

        $records = null;

        foreach ($this->pages->elementModels($prefix) as $class) {
            $elementRule = $this->pages->module($prefix)[$class];

            if ($elementRule['tree'] === $section::class && $elementRule['parent'] !== null) {
                $records = $this->paginate($this->pages->visible($class)->where($elementRule['parent'], $section->getKey()));

                break;
            }
        }

        $default = $this->pages->view($section::class, 'section', 'atrium-theme::list');

        return view($this->pages->template($section, $default), [
            'defaultView' => $default,
            'title' => $view->title(),
            'page' => $view,
            'text' => $view->body(),
            'sections' => $children,
            'records' => $records,
            'canonical' => $this->canonical($request, $url),
            'breadcrumbs' => $breadcrumbs,
            'alternates' => $urls,
            'switch' => $switch,
        ]);
    }

    /**
     * The module's list, then each section above the record, from the top.
     *
     * @param  array{prefix:string,parent:?string,tree:?string,parent_column:string}  $rule
     * @return array<int,array{label:string,url:string}>
     */
    private function breadcrumbs(string $prefix, Model $record, array $rule): array
    {
        $class = $this->pages->elementModels($prefix)[0] ?? $record::class;
        $crumbs = [];
        $tree = $rule['tree'];
        $parentId = $rule['parent'] === null ? null : $record->getAttribute($rule['parent']);

        if ($tree !== null && PublicUrl::ruleFor($tree) !== null) {
            $parentColumn = PublicUrl::ruleFor($tree)['parent_column'];

            for ($depth = 0; $parentId && $depth < self::MAX_DEPTH; $depth++) {
                $section = $this->pages->record($tree, $parentId);

                if ($section === null) {
                    break;
                }

                if (($url = $section->url()) !== null) {
                    array_unshift($crumbs, ['label' => $section->title(), 'url' => $url]);
                }

                $parentId = $section->record->getAttribute($parentColumn);
            }
        }

        array_unshift($crumbs, ['label' => $this->pages->label($class), 'url' => $this->pages->indexUrl($prefix)]);

        return $crumbs;
    }

    /**
     * The pages a visitor can open.
     *
     * @param  array<int,RecordView>  $views
     * @return array<int,RecordView>
     */
    private function withUrls(array $views): array
    {
        return array_values(array_filter($views, static fn (RecordView $view): bool => $view->url() !== null));
    }

    /**
     * As many records a page as the module's `per_page` setting says, if it
     * has one, or `atrium-theme.per_page`. For a cached module the ids come
     * from the cache, then the cards; the query itself otherwise.
     *
     * @param  Builder<Model>  $query
     */
    private function paginate($query): LengthAwarePaginator
    {
        $module = app(Modules::class)->ownerOf($query->getModel()::class);
        $perPage = (int) ($module === null ? null : Setting::get($module->key().'.per_page')) ?: (int) config('atrium-theme.per_page', 20);

        if ($this->pages->isCached($query->getModel()::class)) {
            $page = $query->cachedPaginate($perPage)->withQueryString();
            $page->setCollection($page->getCollection()->map(fn (Card $card): RecordView => RecordView::fromCard($card, $this->pages)));

            return $page;
        }

        $page = $query->paginate($perPage)->withQueryString();
        $page->setCollection($page->getCollection()->map(fn (Model $record): RecordView => new RecordView($record, $this->pages)));

        return $page;
    }

    /**
     * The second and later pages of a list are pages of their own.
     */
    private function canonical(Request $request, string $url): string
    {
        $page = (int) $request->query('page', '1');

        return $page > 1 ? $url.'?page='.$page : $url;
    }

    /**
     * @param  callable(string): string  $url
     * @return array<string,string>
     */
    private function everyLanguage(callable $url): array
    {
        $urls = [];

        foreach ($this->languages->codes() as $code) {
            $urls[$code] = $url($code);
        }

        return $urls;
    }
}
