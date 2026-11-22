<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Cache\Card;
use Atrium\Admin\Cache\RecordCache;
use Atrium\Theme\Pages;
use Atrium\Theme\RecordView;
use Atrium\Theme\Tests\Fixtures\Brand;
use Atrium\Theme\Tests\Fixtures\Product;
use Atrium\Theme\Tests\Fixtures\ProductSection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * A record's page worked out once for its card (toArray) and read back from
 * the card with no query (fromCard): every value, the addresses on the
 * current host kept without it, and the record itself never asked.
 */
final class RecordViewFromCardTest extends TestCase
{
    private function product(array $attributes = []): Product
    {
        $section = ProductSection::query()->create(['name' => 'Laptops', 'code' => 'laptops']);

        return Product::query()->create(['section_id' => $section->id, 'name' => 'Gaming laptop', 'code' => 'gaming', ...$attributes])->fresh();
    }

    /**
     * What the page of the record is, the way the card keeps it.
     */
    private function pageOf(Product $product): RecordView
    {
        return new RecordView($product, $this->app->make(Pages::class));
    }

    /**
     * A card of the product that carries other page data than its record
     * would give: whatever is read from it was not worked out from the model.
     *
     * @param  array<string,mixed>|null  $theme
     */
    private function card(Product $product, ?array $theme): Card
    {
        $card = $this->app->make(RecordCache::class)->cards(Product::class, [$product->getKey()])[0];
        $extra = $theme === null ? [] : ['theme' => $theme];

        return new Card([...$card->toArray(), 'extra' => $extra]);
    }

    public function test_the_page_of_a_record_as_the_card_keeps_it(): void
    {
        $brand = Brand::query()->create(['name' => 'Northwind', 'xml_id' => 'nw']);
        $mouse = $this->product(['name' => 'Mouse', 'code' => 'mouse']);
        $product = $this->product([
            'article' => 'A-1', 'price' => 10, 'color' => 'silver', 'brand' => $brand->id, 'orderable' => 'Y',
            'preview_picture' => '/upload/iblock/p.jpg', 'preview_text' => '<p>Teaser</p>',
            'detail_picture' => 'http://localhost/upload/iblock/d.jpg', 'detail_text' => '<p>Body</p>', 'specs' => '<p>Specs</p>',
            'photos' => json_encode(['/upload/a.jpg', 'products/b.jpg', 'https://cdn.example.com/c.jpg']),
            'manual' => '/upload/files/manual.pdf', 'active_from' => '2026-03-04 10:00:00',
        ]);
        $product->related()->attach($mouse->id);
        // A decimal column reads back as "10" on SQLite and "10.00" on MySQL and PostgreSQL.
        $price = (string) Product::query()->whereKey($product->id)->value('price');

        $this->assertSame([
            'title' => 'Gaming laptop',
            'seo_title' => 'Gaming laptop',
            'seo_description' => 'Teaser',
            'seo_keywords' => null,
            'og_image' => '/upload/iblock/p.jpg',
            'url' => '/catalog/laptops/gaming',
            'path' => 'laptops/gaming',
            'urls' => ['en' => '/catalog/laptops/gaming'],
            'date' => '2026-03-04T10:00:00+00:00',
            'image' => '/upload/iblock/d.jpg',
            'thumbnail' => '/upload/iblock/p.jpg',
            'teaser' => '<p>Teaser</p>',
            'body' => '<p>Body</p>',
            'blocks' => [
                ['kind' => 'properties', 'items' => [
                    ['code' => 'article', 'label' => 'Article', 'value' => 'A-1', 'url' => null, 'links' => []],
                    ['code' => 'price', 'label' => 'Price', 'value' => $price, 'url' => null, 'links' => []],
                    ['code' => 'color', 'label' => 'Color', 'value' => 'Silver', 'url' => null, 'links' => []],
                    ['code' => 'brand', 'label' => 'Brand', 'value' => 'Northwind', 'url' => null, 'links' => []],
                    ['code' => 'related', 'label' => 'Related', 'value' => 'Mouse', 'url' => null, 'links' => [['value' => 'Mouse', 'url' => '/catalog/laptops/mouse']]],
                ]],
                ['kind' => 'html', 'label' => 'Specifications', 'html' => '<p>Specs</p>'],
                ['kind' => 'images', 'label' => 'Photos', 'images' => ['/upload/a.jpg', '/storage/products/b.jpg', 'https://cdn.example.com/c.jpg']],
                ['kind' => 'files', 'label' => 'Manual', 'files' => [['name' => 'manual.pdf', 'url' => '/upload/files/manual.pdf']]],
            ],
        ], $this->pageOf($product)->toArray());
    }

    public function test_a_record_with_nothing_to_show_keeps_nothing_in_its_card(): void
    {
        $product = $this->product();

        $this->assertSame([
            'title' => 'Gaming laptop',
            'seo_title' => 'Gaming laptop',
            'seo_description' => '',
            'seo_keywords' => null,
            'og_image' => null,
            'url' => '/catalog/laptops/gaming',
            'path' => 'laptops/gaming',
            'urls' => ['en' => '/catalog/laptops/gaming'],
            'date' => null,
            'image' => null,
            'thumbnail' => null,
            'teaser' => null,
            'body' => null,
            'blocks' => [],
        ], $this->pageOf($product)->toArray());
    }

    /**
     * @return array<string,array{string,string}>
     */
    public static function addresses(): array
    {
        return [
            'on this host: kept without it' => ['http://localhost/upload/h.jpg', '/upload/h.jpg'],
            'the host alone: the root' => ['http://localhost', '/'],
            'another host with the same beginning' => ['http://localhost.evil.example/x.jpg', 'http://localhost.evil.example/x.jpg'],
            'another scheme' => ['https://localhost/y.jpg', 'https://localhost/y.jpg'],
            'another host' => ['https://cdn.example.com/c.jpg', 'https://cdn.example.com/c.jpg'],
            'a path' => ['/upload/p.jpg', '/upload/p.jpg'],
        ];
    }

    #[DataProvider('addresses')]
    public function test_an_address_on_this_host_is_kept_without_it(string $address, string $kept): void
    {
        $product = $this->product(['photos' => json_encode([$address]), 'detail_picture' => $address]);

        $page = $this->pageOf($product)->toArray();

        $this->assertSame($kept, $page['image']);
        $this->assertSame([$kept], $page['blocks'][0]['images']);
    }

    /**
     * @return array<string,array{string,array<string,mixed>,mixed}>
     */
    public static function readings(): array
    {
        return [
            'the path' => ['path', ['path' => 'a/b'], 'a/b'],
            'no path' => ['path', [], null],
            'the addresses, with the host' => ['urls', ['urls' => ['en' => '/en/x', 'de' => '/de/x']], ['en' => '{root}/en/x', 'de' => '{root}/de/x']],
            'an address in no language is left out' => ['urls', ['urls' => ['en' => '/en/x', 'de' => null, 'fr' => '']], ['en' => '{root}/en/x']],
            'no addresses' => ['urls', [], []],
            'the title' => ['title', ['title' => 'From the card'], 'From the card'],
            'the SEO title' => ['seoTitle', ['seo_title' => 'SEO from the card'], 'SEO from the card'],
            'the SEO description' => ['seoDescription', ['seo_description' => 'Description from the card'], 'Description from the card'],
            'the keywords' => ['seoKeywords', ['seo_keywords' => 'a, b'], 'a, b'],
            'no keywords' => ['seoKeywords', [], null],
            'the social image, with the host' => ['ogImage', ['og_image' => '/upload/og.jpg'], '{root}/upload/og.jpg'],
            'a social image on another host' => ['ogImage', ['og_image' => 'https://cdn.example.com/og.jpg'], 'https://cdn.example.com/og.jpg'],
            'no social image' => ['ogImage', [], null],
            'the address, with the host' => ['url', ['url' => '/catalog/x'], '{root}/catalog/x'],
            'no address' => ['url', [], null],
            'the date' => ['date', ['date' => '2026-03-04T10:00:00+00:00'], '2026-03-04T10:00:00+00:00'],
            'no date' => ['date', [], null],
            'the image, with the host' => ['image', ['image' => '/upload/i.jpg'], '{root}/upload/i.jpg'],
            'no image' => ['image', [], null],
            'the thumbnail, with the host' => ['thumbnail', ['thumbnail' => '/upload/t.jpg'], '{root}/upload/t.jpg'],
            'no thumbnail' => ['thumbnail', [], null],
            'the teaser' => ['teaser', ['teaser' => '<p>Teaser from the card</p>'], '<p>Teaser from the card</p>'],
            'no teaser' => ['teaser', [], null],
            'the body' => ['body', ['body' => '<p>Body from the card</p>'], '<p>Body from the card</p>'],
            'no body' => ['body', [], null],
            'no blocks' => ['blocks', [], []],
        ];
    }

    /**
     * @param  array<string,mixed>  $data
     */
    #[DataProvider('readings')]
    public function test_the_page_is_read_from_the_card_and_not_worked_out_from_the_record(string $method, array $data, mixed $expected): void
    {
        // A record that would answer every one of them differently.
        $product = $this->product([
            'name' => 'Record name', 'code' => 'record', 'preview_picture' => '/upload/record-preview.jpg', 'detail_picture' => '/upload/record-detail.jpg',
            'preview_text' => '<p>Record teaser</p>', 'detail_text' => '<p>Record body</p>', 'active_from' => '2020-01-01 00:00:00', 'photos' => json_encode(['/upload/record.jpg']),
        ]);
        $view = new RecordView($product, $this->app->make(Pages::class), $data);

        $queries = 0;
        DB::listen(function () use (&$queries): void {
            $queries++;
        });
        $value = $view->{$method}();

        $this->assertSame($this->plain($expected), $this->plain($value));
        $this->assertSame(0, $queries);
    }

    /**
     * Dates and texts as plain values, the root of the site put in.
     */
    private function plain(mixed $value): mixed
    {
        return match (true) {
            $value instanceof Carbon => $value->toIso8601String(),
            $value instanceof HtmlString => $value->toHtml(),
            is_array($value) => array_map($this->plain(...), $value),
            is_string($value) => str_replace('{root}', $this->root(), $value),
            default => $value,
        };
    }

    public function test_the_date_of_the_card_speaks_the_language_of_the_page(): void
    {
        app()->setLocale('de');
        $view = new RecordView($this->product(), $this->app->make(Pages::class), ['date' => '2026-03-04T10:00:00+00:00']);

        $this->assertSame('de', $view->date()?->locale);
    }

    public function test_the_blocks_come_back_with_their_texts_and_addresses_on_this_host(): void
    {
        $view = new RecordView($this->product(), $this->app->make(Pages::class), ['blocks' => [
            ['kind' => 'properties', 'items' => [
                ['label' => 'Related', 'value' => 'Mouse', 'url' => null, 'links' => [['value' => 'Mouse', 'url' => '/catalog/mouse'], ['value' => 'Cat', 'url' => null]]],
                ['label' => 'Brand', 'value' => 'Northwind', 'url' => '/brands/northwind'],
                ['code' => 'color', 'label' => 'Color', 'value' => 'Silver'],
            ]],
            ['kind' => 'html', 'label' => 'Specifications', 'html' => '<p>Specs</p>'],
            ['kind' => 'images', 'label' => 'Photos', 'images' => ['/upload/a.jpg', 'https://cdn.example.com/c.jpg']],
            ['kind' => 'files', 'label' => 'Manual', 'files' => [['name' => 'manual.pdf', 'url' => '/upload/manual.pdf']]],
        ]]);

        $blocks = $view->blocks();

        $this->assertEquals([
            ['kind' => 'properties', 'items' => [
                ['code' => '', 'label' => 'Related', 'value' => 'Mouse', 'url' => null, 'links' => [['value' => 'Mouse', 'url' => $this->root().'/catalog/mouse'], ['value' => 'Cat', 'url' => null]]],
                ['code' => '', 'label' => 'Brand', 'value' => 'Northwind', 'url' => $this->root().'/brands/northwind', 'links' => []],
                ['code' => 'color', 'label' => 'Color', 'value' => 'Silver', 'url' => null, 'links' => []],
            ]],
            ['kind' => 'html', 'label' => 'Specifications', 'html' => new HtmlString('<p>Specs</p>')],
            ['kind' => 'images', 'label' => 'Photos', 'images' => [$this->root().'/upload/a.jpg', 'https://cdn.example.com/c.jpg']],
            ['kind' => 'files', 'label' => 'Manual', 'files' => [['name' => 'manual.pdf', 'url' => $this->root().'/upload/manual.pdf']]],
        ], $blocks);
        $this->assertInstanceOf(HtmlString::class, $blocks[1]['html']);
    }

    public function test_a_card_with_a_page_gives_that_page_and_a_card_without_one_gives_the_page_of_its_record(): void
    {
        $product = $this->product();
        $pages = $this->app->make(Pages::class);

        $withPage = RecordView::fromCard($this->card($product, ['title' => 'From the card', 'url' => '/catalog/from-the-card']), $pages);
        $without = RecordView::fromCard($this->card($product, null), $pages);

        $this->assertSame('From the card', $withPage->title());
        $this->assertSame($this->root().'/catalog/from-the-card', $withPage->url());
        $this->assertSame('Gaming laptop', $without->title());
        $this->assertSame($this->root().'/catalog/laptops/gaming', $without->url());
        $this->assertSame($product->getKey(), $withPage->record->getKey());
    }

    public function test_a_card_built_by_the_cache_carries_the_page_of_its_record(): void
    {
        $product = $this->product(['preview_text' => '<p>Teaser</p>']);

        $card = $this->app->make(RecordCache::class)->cards(Product::class, [$product->getKey()])[0];

        $this->assertSame($this->pageOf($product)->toArray(), $card->extra('theme'));
    }
}
