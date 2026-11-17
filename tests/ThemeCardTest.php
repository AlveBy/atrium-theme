<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Cache\RecordCache;
use Atrium\Admin\Models\Language;
use Atrium\Admin\Support\ContentLanguages;
use Atrium\Admin\Support\PublicUrl;
use Atrium\Theme\Pages;
use Atrium\Theme\Tests\Fixtures\Brand;
use Atrium\Theme\Tests\Fixtures\Parcel;
use Illuminate\Support\Facades\DB;

/**
 * What the theme adds to the record cache: the page of a record in its
 * card, for the modules the theme draws pages for, and the pages
 * atrium:cache:warm opens.
 */
final class ThemeCardTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('atrium.public_urls', [...(array) $app['config']->get('atrium.public_urls'), Parcel::class => ['prefix' => 'parcels']]);
    }

    protected function tearDown(): void
    {
        Parcel::$explode = false;

        parent::tearDown();
    }

    private function cache(): RecordCache
    {
        return $this->app->make(RecordCache::class);
    }

    /**
     * @param  array<string,mixed>  $attributes
     */
    private function parcel(string $name, array $attributes = []): Parcel
    {
        return Parcel::query()->create(['name' => $name, 'code' => strtolower($name), ...$attributes]);
    }

    public function test_every_card_of_a_batch_carries_the_page_of_its_record(): void
    {
        $ids = array_map(fn (string $name): int => $this->parcel($name)->id, ['Alpha', 'Beta', 'Gamma']);

        $titles = array_map(fn ($card): mixed => $card->extra('theme')['title'] ?? null, $this->cache()->cards(Parcel::class, $ids));

        $this->assertSame(['Alpha', 'Beta', 'Gamma'], $titles);
    }

    public function test_a_module_with_no_pages_has_no_page_in_its_cards(): void
    {
        $brand = Brand::query()->create(['name' => 'North', 'xml_id' => 'north']);

        $card = $this->cache()->cards(Brand::class, [$brand->id])[0];

        $this->assertNull($card->extra('theme'));
    }

    public function test_a_module_whose_pages_have_no_prefix_has_no_page_in_its_cards(): void
    {
        config(['atrium.public_urls' => [...(array) config('atrium.public_urls'), Brand::class => ['prefix' => '']]]);
        PublicUrl::forgetPaths();
        $brand = Brand::query()->create(['name' => 'North', 'xml_id' => 'north']);

        $card = $this->cache()->cards(Brand::class, [$brand->id])[0];

        $this->assertNull($card->extra('theme'));
    }

    public function test_the_links_of_a_batch_are_loaded_for_all_its_cards_and_forgotten_after(): void
    {
        $north = Brand::query()->create(['name' => 'North', 'xml_id' => 'north']);
        $ids = array_map(fn (string $name): int => $this->parcel($name, ['brand_id' => $north->id])->id, ['Alpha', 'Beta', 'Gamma']);
        $queries = 0;
        DB::listen(function ($query) use (&$queries): void {
            $queries += preg_match('/^select \* from [`"]brands[`"]/', $query->sql);
        });

        $cards = $this->cache()->cards(Parcel::class, $ids);

        // One for the links of the batch, one for the relation: not one a record.
        $this->assertLessThanOrEqual(2, $queries);
        $this->assertSame('North', $cards[2]->extra('theme')['blocks'][0]['items'][0]['value']);
        $this->pagesAsk($north);
    }

    public function test_the_links_of_a_batch_are_forgotten_even_when_a_record_cannot_be_drawn(): void
    {
        $north = Brand::query()->create(['name' => 'North', 'xml_id' => 'north']);
        $ids = [$this->parcel('Alpha', ['brand_id' => $north->id])->id, $this->parcel('boom', ['brand_id' => $north->id])->id];
        $this->cache()->builder()->relationsOf(Parcel::class);
        Parcel::$explode = true;

        $cards = $this->cache()->cards(Parcel::class, $ids);

        $this->assertCount(2, $cards);
        $this->assertNull($cards[0]->extra('theme'));
        $this->pagesAsk($north);
    }

    /**
     * The brand is asked for: nothing of the batch is left in the way.
     */
    private function pagesAsk(Brand $brand): void
    {
        $queries = 0;
        DB::listen(function ($query) use (&$queries): void {
            $queries += preg_match('/^select \* from [`"]brands[`"]/', $query->sql);
        });

        $this->assertSame($brand->id, $this->app->make(Pages::class)->linked(Brand::class, 'id', $brand->id)?->getKey());
        $this->assertSame(1, $queries);
    }

    public function test_the_warm_up_opens_the_home_page_and_the_first_page_of_every_list_in_each_language(): void
    {
        Language::query()->create(['code' => 'de', 'name' => 'Deutsch']);
        $this->app->make(ContentLanguages::class)->flush();
        $pages = $this->app->make(Pages::class);

        $this->assertSame(['/', '/catalog', '/parcels', '/posts'], $this->cache()->warmPaths('en'));
        $this->assertSame(['/', '/de', '/de/catalog', '/de/parcels', '/de/posts'], $this->cache()->warmPaths('de'));
        $this->assertSame(['catalog', 'parcels', 'posts'], $pages->prefixes());
    }
}
