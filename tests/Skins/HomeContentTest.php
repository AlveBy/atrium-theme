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
use Atrium\Theme\Tests\Fixtures\Brand;
use Atrium\Theme\Tests\Fixtures\Home\Faq;
use Atrium\Theme\Tests\Fixtures\Home\Form;
use Atrium\Theme\Tests\Fixtures\Home\Outlet;
use Atrium\Theme\Tests\Fixtures\Home\Page;
use Atrium\Theme\Tests\Fixtures\Home\PageTree;
use Atrium\Theme\Tests\Fixtures\Home\Question;
use Atrium\Theme\Tests\Fixtures\Home\Review;
use Atrium\Theme\Tests\Fixtures\Post;
use Atrium\Theme\Tests\Fixtures\Product;
use Atrium\Theme\Tests\Fixtures\ProductSection;
use Atrium\Theme\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * What a skin's home page asks the modules for: which records, in which
 * order, what is read once per request and what a missing module gives.
 * The modules are stand-ins named in the class map; none is installed.
 */
final class HomeContentTest extends TestCase
{
    /** A class no module brings: "not installed". */
    private const MISSING = 'Atrium\Nothing\Models\Missing';

    protected function setUp(): void
    {
        parent::setUp();

        config(['atrium.public_urls.'.Outlet::class => ['prefix' => 'stores']]);
        $this->app['translator']->addNamespace('atrium-catalog', __DIR__.'/../Fixtures/lang');
        PublicUrl::forgetPaths();
    }

    public function test_the_default_classes_are_the_modules_named_not_required(): void
    {
        $this->assertSame('Atrium\Catalog\Models\Product', HomeContent::PRODUCT);
        $this->assertSame('Atrium\Pages\PageTree', HomeContent::CLASSES['page_tree']);
        $this->assertSame('Atrium\Faq\Faq', HomeContent::CLASSES['faq']);
        $this->assertSame(
            ['product', 'section', 'news', 'store', 'brand', 'review', 'question', 'page', 'form', 'faq', 'page_tree'],
            array_keys(HomeContent::CLASSES),
        );
        $this->assertSame(HomeContent::CLASSES, (new \ReflectionProperty(HomeContent::class, 'classes'))->getValue(app(HomeContent::class)));

        // None of them is there, and the home page still asks.
        $none = app(HomeContent::class);
        $this->assertSame([], $none->products(3));
        $this->assertSame([], $none->sections());
        $this->assertSame([], $none->news(3));
        $this->assertSame([], $none->stores());
        $this->assertCount(0, $none->brands());
        $this->assertCount(0, $none->reviews(3));
        $this->assertCount(0, $none->questions(3));
        $this->assertNull($none->page('home'));
        $this->assertFalse($none->hasForm('question'));
    }

    public function test_products_come_in_order_up_to_the_limit(): void
    {
        $this->product('Third', 'third', 30);
        $this->product('First', 'first', 10);
        $this->product('Second', 'second', 20);
        $this->product('Hidden', 'hidden', 5, active: false);
        $this->product('Without a page', null, 25);

        $home = $this->home();

        $this->assertSame(['First', 'Second', 'Third'], $this->names($home->products(20)));
        $this->assertSame(['First', 'Second'], $this->names($home->products(2)));
        // The limit counts the records read, the one without a page too.
        $this->assertSame(['First', 'Second'], $this->names($home->products(3)));
    }

    public function test_products_of_one_section_only(): void
    {
        $decor = $this->section('Decor', 'decor');
        $light = $this->section('Lighting', 'lighting');
        $this->product('Vase', 'vase', 10, section: $decor);
        $this->product('Lamp', 'lamp', 20, section: $light);
        $this->product('Clock', 'clock', 30, section: $decor);
        $this->product('Loose', 'loose', 40);

        $home = $this->home();

        $this->assertSame(['Vase', 'Clock'], $this->names($home->products(20, $this->viewOf($decor))));
        $this->assertSame(['Lamp'], $this->names($home->products(20, $this->viewOf($light))));
        $this->assertSame(['Vase', 'Lamp', 'Clock', 'Loose'], $this->names($home->products(20)));
    }

    public function test_products_need_their_module_and_their_pages(): void
    {
        $this->product('Vase', 'vase', 10);

        $this->assertSame(['Vase'], $this->names($this->home()->products(3)));
        $this->assertSame([], $this->home(['product' => self::MISSING])->products(3));

        // Installed, but with no pages on the site.
        config(['atrium.public_urls.'.Product::class => null]);
        $this->assertSame([], $this->home()->products(3));
    }

    public function test_sections_are_the_top_ones(): void
    {
        $top = $this->section('Decor', 'decor');
        $this->section('Lighting', 'lighting');
        $this->section('Vases', 'vases', $top);
        $this->section('Hidden', 'hidden', active: false);

        $this->assertSame(['Decor', 'Lighting'], $this->names($this->home()->sections()));
        $this->assertSame([], $this->home(['section' => self::MISSING])->sections());
    }

    public function test_the_menu_has_every_section_with_products(): void
    {
        $decor = $this->section('Decor', 'decor');
        $light = $this->section('Lighting', 'lighting');
        $this->section('Empty', 'empty');
        $this->product('Vase', 'vase', 10, section: $decor);
        $this->product('Clock', 'clock', 20, section: $decor);
        $this->product('Lamp', 'lamp', 30, section: $light);

        $menu = $this->home()->menu(1);

        $this->assertSame(['Decor', 'Lighting'], array_map(fn (array $item): string => $item['section']->title(), $menu));
        $this->assertSame(['Vase'], $this->names($menu[0]['products']));
        $this->assertSame(['Lamp'], $this->names($menu[1]['products']));
        $this->assertSame(['Vase', 'Clock'], $this->names($this->home()->menu(5)[0]['products']));
        $this->assertSame([], $this->home(['product' => self::MISSING])->menu(5));
    }

    public function test_sections_stores_and_pages_are_read_once_per_request(): void
    {
        $this->section('Decor', 'decor');
        $this->outlet('Central', ['+44 20 0000 0000']);
        $this->page('Home', 'home');

        $home = $this->home();
        $this->assertCount(1, $home->sections());
        $this->assertCount(1, $home->stores());
        $page = $home->page('home');
        $this->assertNotNull($page);

        ProductSection::query()->update(['active' => false]);
        Outlet::query()->delete();
        Page::query()->delete();

        $this->assertCount(1, $home->sections());
        $this->assertCount(1, $home->stores());
        $this->assertSame($page, $home->page('home'));
        $this->assertSame('+44 20 0000 0000', $home->phone());

        // A new request reads them again.
        $fresh = $this->home();
        $this->assertSame([], $fresh->sections());
        $this->assertSame([], $fresh->stores());
        $this->assertNull($fresh->page('home'));
        $this->assertNull($fresh->phone());
        $this->assertNull($fresh->hours());
    }

    public function test_news_come_up_to_the_limit(): void
    {
        $this->makePost('Alpha');
        $this->makePost('Beta');
        $this->makePost('Gamma');

        $this->assertSame(['Alpha', 'Beta'], $this->names($this->home()->news(2)));
        $this->assertSame(['Alpha', 'Beta', 'Gamma'], $this->names($this->home()->news(9)));
        $this->assertSame([], $this->home(['news' => self::MISSING])->news(2));
    }

    public function test_stores_are_the_visible_ones_in_order(): void
    {
        $this->outlet('Second', [], 20);
        $this->outlet('First', [], 10);
        $this->outlet('Hidden', [], 5, false);

        $this->assertSame(['First', 'Second'], $this->names($this->home()->stores()));
        $this->assertSame([], $this->home(['store' => self::MISSING])->stores());
    }

    public function test_brands_are_names_and_logos_without_pages(): void
    {
        Brand::query()->create(['name' => 'Glow', 'xml_id' => 'glow']);
        Brand::query()->create(['name' => 'Clay', 'xml_id' => 'clay']);

        $brands = $this->home()->brands();

        $this->assertInstanceOf(Collection::class, $brands);
        $this->assertSame(['Glow', 'Clay'], $brands->pluck('name')->all());

        $none = $this->home(['brand' => self::MISSING])->brands();
        $this->assertInstanceOf(Collection::class, $none);
        $this->assertCount(0, $none);
    }

    public function test_the_newest_published_reviews_come_first(): void
    {
        $this->review('Old', '2026-01-01');
        $this->review('Newest', '2026-03-01');
        $this->review('Tie, first', '2026-02-01');
        $this->review('Tie, second', '2026-02-01');
        $this->review('Unpublished', '2026-04-01', published: false);

        $home = $this->home();

        $this->assertSame(['Newest', 'Tie, second', 'Tie, first', 'Old'], $home->reviews(10)->pluck('name')->all());
        $this->assertSame(['Newest', 'Tie, second'], $home->reviews(2)->pluck('name')->all());
        $this->assertCount(0, $this->home(['review' => self::MISSING])->reviews(2));
    }

    public function test_questions_are_taken_from_the_service_up_to_the_limit(): void
    {
        $home = $this->home();

        $this->assertSame([2, 3], $home->questions(5)->map(fn (Model $question): mixed => $question->getAttribute('id'))->all());
        $this->assertSame([2], $home->questions(1)->map(fn (Model $question): mixed => $question->getAttribute('id'))->all());
        // The keys start again from nothing.
        $this->assertSame([0], $home->questions(1)->keys()->all());
    }

    public function test_questions_need_their_model_and_their_service(): void
    {
        $this->assertCount(0, $this->home(['question' => self::MISSING])->questions(5));
        $this->assertInstanceOf(Collection::class, $this->home(['question' => self::MISSING])->questions(5));
        $this->assertCount(0, $this->home(['faq' => self::MISSING])->questions(5));
        $this->assertCount(2, $this->home()->questions(5));
    }

    public function test_a_page_is_an_active_one_by_its_code(): void
    {
        $this->page('Home', 'home');
        $this->page('Delivery', 'delivery');
        $this->page('Old', 'old', active: false);

        $home = $this->home();

        $this->assertSame('Home', $home->page('home')?->getAttribute('name'));
        $this->assertSame('Delivery', $home->page('delivery')?->getAttribute('name'));
        $this->assertNull($home->page('old'));
        $this->assertNull($home->page('nope'));
        $this->assertNull($this->home(['page' => self::MISSING])->page('home'));
    }

    public function test_the_children_of_a_page_are_its_active_ones_in_order(): void
    {
        $parent = $this->page('About', 'about');
        $other = $this->page('Other', 'other');
        $this->page('Second', 'second', $parent, 20);
        $this->page('First', 'first', $parent, 10);
        $this->page('Tie', 'tie', $parent, 20);
        $this->page('Off', 'off', $parent, 1, false);
        $this->page('Foreign', 'foreign', $other, 1);

        $home = $this->home();

        $this->assertSame(['First', 'Second', 'Tie'], $home->children('about')->pluck('name')->all());
        $this->assertSame(['Foreign'], $home->children('other')->pluck('name')->all());
        $this->assertCount(0, $home->children('first'));

        $none = $home->children('nope');
        $this->assertInstanceOf(Collection::class, $none);
        $this->assertCount(0, $none);
        $this->assertCount(0, $this->home(['page' => self::MISSING])->children('about'));
    }

    public function test_a_page_has_its_address_and_other_records_none(): void
    {
        $this->page('Delivery', 'delivery');
        $brand = Brand::query()->create(['name' => 'Glow', 'xml_id' => 'glow']);
        $home = $this->home();

        $this->assertSame('https://example.test/pages/delivery', $home->pageUrl($home->page('delivery') ?? $this->fail('No page.')));
        $this->assertNull($home->pageUrl($brand));
    }

    public function test_a_form_is_there_when_it_is_active(): void
    {
        Form::query()->create(['code' => 'question']);
        Form::query()->create(['code' => 'callback', 'active' => false]);
        $home = $this->home();

        $this->assertTrue($home->hasForm('question'));
        $this->assertFalse($home->hasForm('callback'));
        $this->assertFalse($home->hasForm('nope'));
        $this->assertFalse($this->home(['form' => self::MISSING])->hasForm('question'));
    }

    public function test_a_module_s_list_page_only_when_it_has_pages_on_the_site(): void
    {
        $home = $this->home();

        $this->assertStringEndsWith('/catalog', (string) $home->indexUrl(Product::class));
        $this->assertStringEndsWith('/posts', (string) $home->indexUrl(Post::class));
        $this->assertNull(PublicUrl::ruleFor(Brand::class));
        $this->assertNull($home->indexUrl(Brand::class));

        // A rule for a class that is not there.
        config(['atrium.public_urls.'.self::MISSING => ['prefix' => 'ghosts']]);
        $this->assertNull($home->indexUrl(self::MISSING));
    }

    public function test_the_header_shows_the_first_store_s_phone_and_hours(): void
    {
        $this->outlet('First', ['+44 20 0000 0000', '+44 20 1111 1111'], 10, detail: '<p>10:00–21:00, seven days a week</p><p>Parking</p>');
        $this->outlet('Second', ['+44 20 2222 2222'], 20, detail: '<p>11:00–22:00, seven days a week</p>');
        $home = $this->home();

        $this->assertSame('+44 20 0000 0000', $home->phone());
        $this->assertSame('10:00–21:00, seven days a week', $home->hours());
        $this->assertSame('11:00–22:00, seven days a week', $home->hours($home->stores()[1]));
    }

    public function test_the_phone_is_text_or_nothing(): void
    {
        $this->assertNull($this->home()->phone());

        $this->outlet('First', []);
        $this->assertNull($this->home()->phone());

        Outlet::query()->update(['phones' => json_encode([7000])]);
        $this->assertNull($this->home()->phone());

        Outlet::query()->update(['phones' => json_encode(['+44 20 0000 0000'])]);
        $this->assertSame('+44 20 0000 0000', $this->home()->phone());
    }

    public function test_a_product_s_badge_is_its_label_in_the_page_s_language(): void
    {
        $home = $this->home();

        $this->assertSame('New', $home->badge($this->viewWith(['label' => 'new'])));
        $this->assertSame('Hit', $home->badge($this->viewWith(['label' => 'hit'])));
        $this->assertNull($home->badge($this->viewWith([])));
        $this->assertNull($home->badge($this->viewWith(['label' => ''])));
        $this->assertNull($home->badge($this->viewWith(['label' => 5])));
    }

    public function test_a_product_s_brand_is_its_name_when_it_has_one(): void
    {
        $home = $this->home();
        $view = $this->viewOf($this->product('Vase', 'vase', 10));

        $this->assertNull($home->brand($view));

        $view->record->setRelation('brand', new Brand(['name' => 'Clay & Co']));
        $this->assertSame('Clay & Co', $home->brand($view));

        $view->record->setRelation('brand', new Brand(['name' => '']));
        $this->assertNull($home->brand($view));

        $view->record->setRelation('brand', new Brand(['name' => 5]));
        $this->assertNull($home->brand($view));

        $view->record->setRelation('brand', null);
        $this->assertNull($home->brand($view));
    }

    public function test_a_review_names_what_it_is_about(): void
    {
        $product = $this->product('Wool throw', 'wool-throw', 10);
        $about = Review::query()->create(['name' => 'About', 'reviewable_type' => Product::class, 'reviewable_id' => $product->getKey()]);
        $nothing = Review::query()->create(['name' => 'Nothing']);
        $home = $this->home();

        $this->assertSame('Wool throw', $home->subject($about));
        $this->assertNull($home->subject($nothing));

        $product->delete();
        $this->assertNull($home->subject($about->refresh()));
    }

    private function product(string $name, ?string $code, int $sort, bool $active = true, ?Model $section = null): Product
    {
        return Product::query()->create([
            'name' => $name,
            'code' => $code,
            'sort' => $sort,
            'active' => $active,
            'section_id' => $section?->getKey(),
        ]);
    }

    private function section(string $name, string $code, ?Model $parent = null, bool $active = true): ProductSection
    {
        return ProductSection::query()->create(['name' => $name, 'code' => $code, 'parent_id' => $parent?->getKey(), 'active' => $active]);
    }

    private function makePost(string $name): Post
    {
        return Post::query()->create(['name' => ['en' => $name], 'code' => ['en' => strtolower($name)]]);
    }

    /**
     * @param  array<int,string>  $phones
     */
    private function outlet(string $name, array $phones, int $sort = 500, bool $active = true, ?string $detail = null): Outlet
    {
        return Outlet::query()->create([
            'name' => $name,
            'code' => strtolower($name),
            'phones' => $phones,
            'sort' => $sort,
            'active' => $active,
            'detail_text' => $detail,
        ]);
    }

    private function review(string $name, string $postedAt, bool $published = true): Review
    {
        return Review::query()->create(['name' => $name, 'posted_at' => $postedAt, 'published' => $published]);
    }

    private function page(string $name, string $code, ?Model $parent = null, int $sort = 500, bool $active = true): Page
    {
        return Page::query()->create(['name' => $name, 'code' => $code, 'parent_id' => $parent?->getKey(), 'sort' => $sort, 'active' => $active]);
    }

    /**
     * The home page's content over the stand-in modules; $classes replaces some.
     *
     * @param  array<string,string>  $classes
     */
    private function home(array $classes = []): HomeContent
    {
        return new HomeContent(app(Pages::class), [
            'product' => Product::class,
            'section' => ProductSection::class,
            'news' => Post::class,
            'store' => Outlet::class,
            'brand' => Brand::class,
            'review' => Review::class,
            'question' => Question::class,
            'page' => Page::class,
            'form' => Form::class,
            'faq' => Faq::class,
            'page_tree' => PageTree::class,
            ...$classes,
        ]);
    }

    private function viewOf(Model $record): RecordView
    {
        return new RecordView($record, app(Pages::class));
    }

    /**
     * @param  array<string,mixed>  $attributes
     */
    private function viewWith(array $attributes): RecordView
    {
        return $this->viewOf((new Product)->forceFill($attributes));
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
