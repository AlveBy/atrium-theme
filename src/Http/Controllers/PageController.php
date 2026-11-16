<?php

declare(strict_types=1);

namespace Atrium\Theme\Http\Controllers;

use Atrium\Admin\Support\ContentLanguages;
use Atrium\Admin\Support\PublicUrl;
use Atrium\Theme\Pages;
use Atrium\Theme\RecordView;
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

    public function home(): View
    {
        $modules = [];

        foreach ($this->pages->prefixes() as $prefix) {
            $class = $this->pages->elementModels($prefix)[0] ?? $this->pages->sectionModels($prefix)[0];

            $modules[] = [
                'label' => $this->pages->label($class),
                'url' => $this->pages->indexUrl($prefix),
                'records' => $this->views($this->pages->visible($class)->limit((int) config('atrium-theme.home_items', 3))->get()),
            ];
        }

        $switch = $this->everyLanguage(fn (string $code): string => $this->pages->homeUrl($code));

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
            $sections = [...$sections, ...$this->views($this->pages->visible($class)->whereNull($rule['parent_column'])->get())];
        }

        $class = $this->pages->elementModels($prefix)[0] ?? null;
        $records = $class === null ? null : $this->paginate($this->pages->visible($class));
        $switch = $this->everyLanguage(fn (string $code): string => $this->pages->indexUrl($prefix, $code));

        return view('atrium-theme::list', [
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

        $url = $this->pages->url($record);

        abort_if($url === null, 404);

        // Found by its code under another path: send the visitor and search
        // engines to where it lives now.
        if ($this->pages->path($record) !== trim($path, '/')) {
            return redirect()->to($url, 301);
        }

        $rule = $this->pages->module($prefix)[$record::class];
        $urls = $this->pages->urls($record);
        $switch = $this->everyLanguage(fn (string $code): string => $urls[$code] ?? $this->pages->homeUrl($code));
        $view = new RecordView($record, $this->pages);
        $breadcrumbs = $this->breadcrumbs($prefix, $record, $rule);

        if ($this->pages->isSectionModel($record::class, $rule)) {
            return $this->section($request, $prefix, $record, $view, $url, $breadcrumbs, $urls, $switch);
        }

        return view('atrium-theme::page', [
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
        $children = $this->views($this->pages->visible($section::class)->where($rule['parent_column'], $section->getKey())->get());

        $records = null;

        foreach ($this->pages->elementModels($prefix) as $class) {
            $elementRule = $this->pages->module($prefix)[$class];

            if ($elementRule['tree'] === $section::class && $elementRule['parent'] !== null) {
                $records = $this->paginate($this->pages->visible($class)->where($elementRule['parent'], $section->getKey()));

                break;
            }
        }

        return view('atrium-theme::list', [
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
                $section = $tree::query()->find($parentId);

                if (! $section instanceof Model) {
                    break;
                }

                if (($url = $this->pages->url($section)) !== null) {
                    array_unshift($crumbs, ['label' => (new RecordView($section, $this->pages))->title(), 'url' => $url]);
                }

                $parentId = $section->getAttribute($parentColumn);
            }
        }

        array_unshift($crumbs, ['label' => $this->pages->label($class), 'url' => $this->pages->indexUrl($prefix)]);

        return $crumbs;
    }

    /**
     * @param  iterable<Model>  $records
     * @return array<int,RecordView>
     */
    private function views(iterable $records): array
    {
        $views = [];

        foreach ($records as $record) {
            $view = new RecordView($record, $this->pages);

            if ($view->url() !== null) {
                $views[] = $view;
            }
        }

        return $views;
    }

    /**
     * @param  Builder<Model>  $query
     */
    private function paginate($query): LengthAwarePaginator
    {
        $page = $query->paginate((int) config('atrium-theme.per_page', 20))->withQueryString();
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
