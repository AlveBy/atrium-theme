<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Skins;

use Atrium\Admin\Support\PublicUrl;
use Atrium\Theme\Pages;
use Atrium\Theme\RecordView;
use Atrium\Theme\Skins\HomeContent;
use Illuminate\Database\Eloquent\Model;

/**
 * What a skin's home page asks the modules for, on the shop's content:
 * which records, in which order, and what is read once per request.
 */
final class HomeContentTest extends SkinsTestCase
{
    private const PRODUCTS = ['Drop vase', 'Desk lamp', 'Mug, 350 ml', 'Oak chair', 'Terracotta pot', 'Wool throw', 'Salad bowl', 'Wall clock', 'Wall shelf'];

    protected function modules(): array
    {
        return self::ALL_MODULES;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedSubject('shop');
    }

    public function test_products_come_in_order_up_to_the_limit_and_by_section(): void
    {
        $home = $this->home();

        $this->assertSame(self::PRODUCTS, $this->names($home->products(20)));
        $this->assertSame(['Drop vase', 'Desk lamp'], $this->names($home->products(2)));

        $decor = $this->section('decor');
        $this->assertSame(['Drop vase', 'Wall clock'], $this->names($home->products(20, $decor)));
    }

    public function test_the_menu_has_every_section_with_products(): void
    {
        $menu = $this->home()->menu(1);

        $this->assertSame(['Tableware', 'Lighting', 'Furniture', 'Textiles', 'Plants', 'Decor'], array_map(fn (array $item): string => $item['section']->title(), $menu));
        $this->assertSame(['Mug, 350 ml'], $this->names($menu[0]['products']));

        // A section with no products is left out.
        HomeContent::PRODUCT::query()->where('section_id', $this->section('lighting')->record->getKey())->delete();
        $this->assertNotContains('Lighting', array_map(fn (array $item): string => $item['section']->title(), $this->home()->menu(1)));
    }

    public function test_sections_stores_and_pages_are_read_once_per_request(): void
    {
        $home = $this->home();
        $this->assertCount(6, $home->sections());
        $this->assertCount(2, $home->stores());
        $page = $home->page('home');
        $this->assertNotNull($page);

        HomeContent::SECTION::query()->update(['active' => false]);
        HomeContent::STORE::query()->delete();
        HomeContent::PAGE::query()->delete();

        $this->assertCount(6, $home->sections());
        $this->assertCount(2, $home->stores());
        $this->assertSame($page, $home->page('home'));

        // A new request reads them again.
        $fresh = $this->home();
        $this->assertSame([], $fresh->sections());
        $this->assertSame([], $fresh->stores());
        $this->assertNull($fresh->page('home'));
        $this->assertNull($fresh->phone());
        $this->assertNull($fresh->hours());
    }

    public function test_the_header_shows_the_first_store_s_phone_and_hours(): void
    {
        $home = $this->home();

        $this->assertSame('+44 20 0000 0000', $home->phone());
        $this->assertSame('10:00–21:00, seven days a week', $home->hours());
        $this->assertSame('11:00–22:00, seven days a week', $home->hours($home->stores()[1]));
    }

    public function test_a_page_has_its_address_and_other_records_none(): void
    {
        $home = $this->home();

        $this->assertStringEndsWith('/delivery', (string) $home->pageUrl($home->page('delivery')));
        $this->assertNull($home->pageUrl(HomeContent::BRAND::query()->firstOrFail()));
        $this->assertNull($home->page('nope'));
        $this->assertCount(0, $home->children('nope'));
    }

    public function test_a_module_s_list_page_only_when_it_has_pages_on_the_site(): void
    {
        $home = $this->home();

        $this->assertStringEndsWith('/catalog', (string) $home->indexUrl(HomeContent::PRODUCT));
        $this->assertNull(PublicUrl::ruleFor(HomeContent::BRAND));
        $this->assertNull($home->indexUrl(HomeContent::BRAND));
    }

    public function test_a_product_s_badge_is_its_label_in_the_page_s_language(): void
    {
        $home = $this->home();

        $this->assertSame('New', $home->badge($this->product('drop-vase')));
        $this->assertNull($home->badge($this->product('desk-lamp')));

        HomeContent::PRODUCT::query()->where('code', 'desk-lamp')->update(['label' => '']);
        $this->assertNull($home->badge($this->product('desk-lamp')));
    }

    public function test_a_product_s_brand_is_its_name_when_it_has_one(): void
    {
        $home = $this->home();

        $this->assertSame('Clay & Co', $home->brand($this->product('drop-vase')));

        HomeContent::BRAND::query()->where('code', 'glow')->update(['name' => '']);
        $this->assertNull($home->brand($this->product('desk-lamp')));

        HomeContent::PRODUCT::query()->where('code', 'mug')->update(['brand_id' => null]);
        $this->assertNull($home->brand($this->product('mug')));
    }

    public function test_a_review_names_what_it_is_about(): void
    {
        $home = $this->home();

        $this->assertSame(['Wool throw', 'Drop vase', 'Oak chair'], $home->reviews(10)->map(fn (Model $review): ?string => $home->subject($review))->all());

        $about = HomeContent::REVIEW::query()->firstOrFail();
        $about->forceFill(['reviewable_type' => null, 'reviewable_id' => null])->save();
        $this->assertNull($home->subject($about->refresh()));

        $gone = HomeContent::REVIEW::query()->whereNotNull('reviewable_id')->firstOrFail();
        HomeContent::PRODUCT::query()->whereKey($gone->getAttribute('reviewable_id'))->delete();
        $this->assertNull($home->subject($gone->refresh()));
    }

    public function test_a_skin_renames_a_module_in_the_menu(): void
    {
        $pages = app(Pages::class);

        config(['atrium-theme.skin' => null]);
        $plain = $pages->label(HomeContent::PRODUCT);

        config(['atrium-theme.skin' => 'travel']);
        $this->assertSame('Tours', $pages->label(HomeContent::PRODUCT));
        $this->assertNotSame('Tours', $plain);
    }

    private function home(): HomeContent
    {
        return new HomeContent(app(Pages::class));
    }

    private function product(string $code): RecordView
    {
        return new RecordView(HomeContent::PRODUCT::query()->where('code', $code)->firstOrFail(), app(Pages::class));
    }

    private function section(string $code): RecordView
    {
        return new RecordView(HomeContent::SECTION::query()->where('code', $code)->firstOrFail(), app(Pages::class));
    }

    /**
     * @param  array<int,RecordView>  $views
     * @return array<int,string>
     */
    private function names(array $views): array
    {
        return array_map(fn (RecordView $view): string => $view->title(), $views);
    }
}
