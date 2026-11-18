<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Skins;

use Atrium\Admin\Modules\Soft;
use Atrium\Admin\Support\PublicUrl;
use Atrium\Faq\Models\Question;
use Atrium\Pages\PageTree;
use Atrium\Theme\Pages;
use Atrium\Theme\RecordView;
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

    public function __construct(private readonly Pages $pages) {}

    /**
     * Products of the catalog, of one section when given.
     *
     * @return array<int,RecordView>
     */
    public function products(int $limit, ?RecordView $section = null): array
    {
        if (! $this->hasPages(self::PRODUCT)) {
            return [];
        }

        $query = $this->pages->visible(self::PRODUCT)->limit($limit);

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
        if (! $this->hasPages(self::SECTION)) {
            return [];
        }

        return $this->sections ??= $this->views($this->pages->visible(self::SECTION)->whereNull('parent_id')->get());
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
        return $this->hasPages(self::NEWS) ? $this->views($this->pages->visible(self::NEWS)->limit($limit)->get()) : [];
    }

    /**
     * @return array<int,RecordView>
     */
    public function stores(): array
    {
        return $this->stores ??= $this->hasPages(self::STORE) ? $this->views($this->pages->visible(self::STORE)->get()) : [];
    }

    /**
     * Brands have no pages of their own: they are names, logos and links.
     *
     * @return Collection<int,Model>
     */
    public function brands(): Collection
    {
        return Soft::available(self::BRAND) ? $this->pages->visible(self::BRAND)->get() : new Collection;
    }

    /**
     * The newest published reviews of the site and its records.
     *
     * @return Collection<int,\Atrium\Reviews\Models\Review>
     */
    public function reviews(int $limit): Collection
    {
        if (! Soft::available(self::REVIEW)) {
            return new Collection;
        }

        return self::REVIEW::query()->published()->orderByDesc('posted_at')->orderByDesc('id')->limit($limit)->get();
    }

    /**
     * Questions answered in the page's language.
     *
     * @return Collection<int,Question>
     */
    public function questions(int $limit): Collection
    {
        if (! Soft::available(self::QUESTION) || ! class_exists('Atrium\Faq\Faq')) {
            return new Collection;
        }

        return app('Atrium\Faq\Faq')->questions()->take($limit)->values();
    }

    /**
     * An active page of the Pages module by its code.
     */
    public function page(string $code): ?Model
    {
        if (! Soft::available(self::PAGE)) {
            return null;
        }

        return $this->pagesByCode[$code] ??= self::PAGE::query()->where('code', $code)->where('active', true)->first();
    }

    /**
     * The active pages right below a page, in the editors' order.
     *
     * @return Collection<int,\Atrium\Pages\Models\Page>
     */
    public function children(string $code): Collection
    {
        $page = $this->page($code);

        if ($page === null) {
            return new Collection;
        }

        return self::PAGE::query()->where('parent_id', $page->getKey())->where('active', true)->orderBy('sort')->orderBy('id')->get();
    }

    public function pageUrl(Model $page): ?string
    {
        return $page instanceof \Atrium\Pages\Models\Page ? app(PageTree::class)->url($page) : null;
    }

    /**
     * Whether a form of the Forms module is there to be drawn.
     */
    public function hasForm(string $code): bool
    {
        return Soft::available(self::FORM) && self::FORM::query()->where('code', $code)->where('active', true)->exists();
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
