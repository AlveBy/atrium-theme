<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Skins;

use Atrium\Admin\Modules\Soft;
use Atrium\Admin\Support\PublicUrl;
use Atrium\Theme\Pages;
use Atrium\Theme\RecordView;
use Illuminate\Database\Eloquent\Collection as ModelCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;

/**
 * What a skin's home page is made of, read from the modules: the catalog,
 * news, reviews, questions, stores, brands, pages and forms. A module that
 * is not installed gives nothing, and the section that shows it is left
 * out. The modules are named, not required: none of them has to be there.
 */
final class HomeContent
{
    public const PRODUCT = 'Atrium\Catalog\Models\Product';

    public const SECTION = 'Atrium\Catalog\Models\CatalogSection';

    public const NEWS = 'Atrium\News\Models\News';

    public const STORE = 'Atrium\Stores\Models\Store';

    public const BRAND = 'Atrium\Brands\Models\Brand';

    public const REVIEW = 'Atrium\Reviews\Models\Review';

    public const QUESTION = 'Atrium\Faq\Models\Question';

    public const CATEGORY = 'Atrium\Faq\Models\Category';

    public const PAGE = 'Atrium\Pages\Models\Page';

    public const FORM = 'Atrium\Forms\Models\Form';

    public const MEMBER = 'Atrium\Team\Models\Member';

    /**
     * The classes of the modules by what the home page takes from them: the
     * models, and the two services that know more than a model does. A site
     * or a test may name others; the modules are not required, only named.
     */
    public const CLASSES = [
        'product' => self::PRODUCT,
        'section' => self::SECTION,
        'news' => self::NEWS,
        'store' => self::STORE,
        'brand' => self::BRAND,
        'review' => self::REVIEW,
        'question' => self::QUESTION,
        'page' => self::PAGE,
        'form' => self::FORM,
        'member' => self::MEMBER,
        'faq' => 'Atrium\Faq\Faq',
        'page_tree' => 'Atrium\Pages\PageTree',
    ];

    /**
     * What a page asks for more than once, read once per request.
     *
     * @var array<int,RecordView>|null
     */
    private ?array $sections = null;

    /** @var array<int,RecordView>|null */
    private ?array $stores = null;

    /** @var array<string,Model|null> */
    private array $pagesByCode = [];

    /**
     * @param  array<string,class-string>  $classes  see CLASSES
     */
    public function __construct(private readonly Pages $pages, private readonly array $classes = self::CLASSES) {}

    /**
     * Products of the catalog, of one section when given.
     *
     * @return array<int,RecordView>
     */
    public function products(int $limit, ?RecordView $section = null): array
    {
        if (! $this->hasPages($this->classes['product'])) {
            return [];
        }

        $query = $this->pages->visible($this->classes['product'])->limit($limit);

        if ($section !== null) {
            $query->where('section_id', $section->record->getKey());
        }

        return $this->views($query->get());
    }

    /**
     * The top sections of the catalog.
     *
     * @return array<int,RecordView>
     */
    public function sections(): array
    {
        if (! $this->hasPages($this->classes['section'])) {
            return [];
        }

        return $this->sections ??= $this->views($this->pages->visible($this->classes['section'])->whereNull('parent_id')->get());
    }

    /**
     * A picture for a section: its own, or the first one of a product in
     * it or in a section below it.
     */
    public function sectionPicture(RecordView $section): ?string
    {
        if (($own = $section->thumbnail()) !== null || ! $this->hasPages($this->classes['product'])) {
            return $own;
        }

        $parents = $this->pages->visible($this->classes['section'])->pluck('parent_id', 'id')->all();
        $ids = [$section->record->getKey()];

        do {
            $known = count($ids);
            $ids = array_keys(array_filter($parents, static fn ($parent, $id): bool => in_array($id, $ids, true) || in_array($parent, $ids, true), ARRAY_FILTER_USE_BOTH));
        } while (count($ids) > $known);

        foreach ($this->pages->visible($this->classes['product'])->whereIn('section_id', $ids)->cursor() as $product) {
            foreach ($this->views([$product]) as $view) {
                if (($picture = $view->thumbnail()) !== null) {
                    return $picture;
                }
            }
        }

        return null;
    }

    /**
     * Each top section with its products, for a menu: sections without
     * products are left out.
     *
     * @return array<int,array{section:RecordView,products:array<int,RecordView>}>
     */
    public function menu(int $perSection): array
    {
        $menu = [];

        foreach ($this->sections() as $section) {
            if (($products = $this->products($perSection, $section)) !== []) {
                $menu[] = ['section' => $section, 'products' => $products];
            }
        }

        return $menu;
    }

    /**
     * @return array<int,RecordView>
     */
    public function news(int $limit): array
    {
        return $this->hasPages($this->classes['news']) ? $this->views($this->pages->visible($this->classes['news'])->limit($limit)->get()) : [];
    }

    /**
     * @return array<int,RecordView>
     */
    public function stores(): array
    {
        return $this->stores ??= $this->hasPages($this->classes['store']) ? $this->views($this->pages->visible($this->classes['store'])->get()) : [];
    }

    /**
     * The people of the team, in the editors' order.
     *
     * @return array<int,RecordView>
     */
    public function team(int $limit): array
    {
        return $this->hasPages($this->classes['member']) ? $this->views($this->pages->visible($this->classes['member'])->limit($limit)->get()) : [];
    }

    /**
     * The records a product is shown with ("goes well with") that a
     * visitor can open. A model without such a relation has none.
     *
     * @return array<int,RecordView>
     */
    public function related(RecordView $product, int $limit): array
    {
        $related = $product->record->getRelationValue('related');

        if (! $related instanceof ModelCollection || $related->isEmpty()) {
            return [];
        }

        return $this->views($this->pages->visible($product->record::class)->whereKey($related->modelKeys())->limit($limit)->get());
    }

    /**
     * Brands have no pages of their own: they are names, logos and links.
     *
     * @return Collection<int,Model>
     */
    public function brands(): Collection
    {
        return Soft::available($this->classes['brand']) ? $this->pages->visible($this->classes['brand'])->get() : new Collection;
    }

    /**
     * The newest published reviews of the site and its records.
     *
     * @return Collection<int,Model>
     */
    public function reviews(int $limit): Collection
    {
        if (! Soft::available($this->classes['review'])) {
            return new Collection;
        }

        return $this->classes['review']::query()->scopes(['published'])->orderByDesc('posted_at')->orderByDesc('id')->limit($limit)->get();
    }

    /**
     * Questions answered in the page's language.
     *
     * @return Collection<int,Model>
     */
    public function questions(int $limit): Collection
    {
        if (! Soft::available($this->classes['question']) || ! class_exists($this->classes['faq'])) {
            return new Collection;
        }

        return app($this->classes['faq'])->questions()->take($limit)->values();
    }

    /**
     * An active page of the Pages module by its code.
     */
    public function page(string $code): ?Model
    {
        if (! Soft::available($this->classes['page'])) {
            return null;
        }

        return $this->pagesByCode[$code] ??= $this->classes['page']::query()->where('code', $code)->where('active', true)->first();
    }

    /**
     * The active pages right below a page, in the editors' order.
     *
     * @return Collection<int,Model>
     */
    public function children(string $code): Collection
    {
        $page = $this->page($code);

        if ($page === null) {
            return new Collection;
        }

        return $this->classes['page']::query()->where('parent_id', $page->getKey())->where('active', true)->orderBy('sort')->orderBy('id')->get();
    }

    public function pageUrl(Model $page): ?string
    {
        return $page instanceof $this->classes['page'] ? app($this->classes['page_tree'])->url($page) : null;
    }

    /**
     * Whether a form of the Forms module is there to be drawn.
     */
    public function hasForm(string $code): bool
    {
        return Soft::available($this->classes['form']) && $this->classes['form']::query()->where('code', $code)->where('active', true)->exists();
    }

    /**
     * The list page of a module, when it has pages on the site.
     */
    public function indexUrl(string $class): ?string
    {
        $rule = $this->hasPages($class) ? PublicUrl::ruleFor($class) : null;

        return $rule === null ? null : $this->pages->indexUrl($rule['prefix']);
    }

    /**
     * The phone of the first store, for the header.
     */
    public function phone(): ?string
    {
        $phone = data_get($this->stores()[0] ?? null, 'record.phones.0');

        return is_string($phone) ? $phone : null;
    }

    /**
     * Opening hours of a store: the first paragraph of its description.
     */
    public function hours(?RecordView $store = null): ?string
    {
        $store ??= $this->stores()[0] ?? null;
        $text = (string) $store?->record->getAttribute('detail_text');

        if (! preg_match('#<p[^>]*>.*?</p>#s', $text, $match)) {
            return null;
        }

        $hours = trim(html_entity_decode(strip_tags($match[0]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        return $hours === '' ? null : $hours;
    }

    /**
     * The first line of a store's address.
     */
    public function address(RecordView $store): string
    {
        return trim(strtok((string) $store->record->getAttribute('address'), "\n") ?: '');
    }

    /**
     * A product's price in the site's currency (atrium-theme.currency) as
     * the page's language writes it: "from €1,250", "ab 1.250 €".
     */
    public function price(RecordView $product, Skin $skin): ?string
    {
        $price = $product->record->getAttribute('price');

        if (! is_numeric($price) || $price <= 0) {
            return null;
        }

        $amount = Number::currency((float) $price, (string) config('atrium-theme.currency'), app()->getLocale(), fmod((float) $price, 1.0) === 0.0 ? 0 : 2);

        return $skin->text('price', ['price' => $amount]);
    }

    /**
     * The badge of a product (new, hit, sale) as the catalog names it.
     */
    public function badge(RecordView $product): ?string
    {
        $label = $product->record->getAttribute('label');

        return is_string($label) && $label !== '' ? __('atrium-catalog::catalog.options.label.'.$label) : null;
    }

    /**
     * The name of a product's brand. Without the Brands module the
     * product's relation finds none.
     */
    public function brand(RecordView $product): ?string
    {
        $brand = $product->record->getRelationValue('brand');
        $name = $brand instanceof Model ? $brand->getAttribute('name') : null;

        return is_string($name) && $name !== '' ? $name : null;
    }

    /**
     * What a review is about ("Karelia"), when it is about a record.
     */
    public function subject(Model $review): ?string
    {
        $record = $review->getAttribute('reviewable_type') === null ? null : $review->getRelationValue('reviewable');

        return $record instanceof Model ? (new RecordView($record, $this->pages))->title() : null;
    }

    private function hasPages(string $class): bool
    {
        return Soft::available($class) && PublicUrl::ruleFor($class) !== null;
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
}
