<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Theme\Pages;
use Atrium\Theme\RecordView;
use Atrium\Theme\Tests\Fixtures\Brand;
use Atrium\Theme\Tests\Fixtures\Gadget;
use Atrium\Theme\Tests\Fixtures\Parcel;
use Atrium\Theme\Tests\Fixtures\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * The records a page links to: loaded for a whole batch at once, one
 * query a field, and read from the relation a card already carries.
 */
final class ParcelLinksTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('atrium.public_urls', [...(array) $app['config']->get('atrium.public_urls'), Parcel::class => ['prefix' => 'parcels']]);
    }

    private function pages(): Pages
    {
        return $this->app->make(Pages::class);
    }

    private function brand(string $name, string $xml = ''): Brand
    {
        return Brand::query()->create(['name' => $name, 'xml_id' => $xml ?: strtolower($name)]);
    }

    /**
     * @param  array<string,mixed>  $attributes
     */
    private function parcel(array $attributes = []): Parcel
    {
        return Parcel::query()->create(['name' => 'Parcel', 'code' => 'parcel-'.(Parcel::query()->count() + 1), ...$attributes]);
    }

    /**
     * What a callback asks of the table of brands: the values of each query.
     *
     * @return array<int,array<int,mixed>>
     */
    private function brandQueries(callable $callback): array
    {
        $queries = [];
        DB::listen(function ($query) use (&$queries): void {
            if (preg_match('/^select \* from [`"]brands[`"]/', $query->sql) === 1) {
                $queries[] = $query->bindings;
            }
        });
        $callback();

        return $queries;
    }

    /**
     * @param  array<int,mixed>  $values
     * @return array<int,int|string>
     */
    private function normal(array $values): array
    {
        return array_map(fn (mixed $value): int|string => is_numeric($value) ? (int) $value : (string) $value, $values);
    }

    /**
     * @return Collection<int,Model>
     */
    private function batch(Model ...$records): Collection
    {
        return new Collection(array_values($records));
    }

    private function pageOf(Model $parcel): RecordView
    {
        return new RecordView($parcel, $this->pages());
    }

    // preloadLinks and linked

    public function test_a_batch_loads_the_records_it_links_to_in_one_query(): void
    {
        $north = $this->brand('North');
        $south = $this->brand('South');
        $parcels = $this->batch(
            $this->parcel(['brand_id' => $north->id]),
            $this->parcel(['brand_id' => $north->id]),
            $this->parcel(['brand_id' => null]),
            $this->parcel(['brand_id' => $south->id]),
            $this->parcel(['brand_id' => 999_999]),
        );

        $queries = $this->brandQueries(fn () => $this->pages()->preloadLinks($parcels));

        $this->assertCount(1, $queries);
        $this->assertSame([$north->id, $south->id, 999_999], $this->normal($queries[0]));
    }

    public function test_what_a_batch_loaded_is_read_with_no_query(): void
    {
        $north = $this->brand('North');
        $this->pages()->preloadLinks($this->batch($this->parcel(['brand_id' => $north->id]), $this->parcel(['brand_id' => 999_999])));

        $queries = $this->brandQueries(function () use ($north): void {
            $this->assertSame($north->id, $this->pages()->linked(Brand::class, 'id', $north->id)?->getKey());
            $this->assertNull($this->pages()->linked(Brand::class, 'id', 999_999));
        });

        $this->assertSame([], $queries);
    }

    public function test_a_record_the_batch_did_not_load_is_asked_for(): void
    {
        $north = $this->brand('North');
        $this->pages()->preloadLinks($this->batch($this->parcel(['brand_id' => $this->brand('South')->id])));

        $queries = $this->brandQueries(fn () => $this->assertSame($north->id, $this->pages()->linked(Brand::class, 'id', $north->id)?->getKey()));

        $this->assertCount(1, $queries);
    }

    public function test_a_second_batch_loads_only_what_the_first_did_not(): void
    {
        $north = $this->brand('North');
        $south = $this->brand('South');
        $this->pages()->preloadLinks($this->batch($this->parcel(['brand_id' => $north->id])));

        $queries = $this->brandQueries(fn () => $this->pages()->preloadLinks($this->batch(
            $this->parcel(['brand_id' => $north->id]),
            $this->parcel(['brand_id' => $south->id]),
        )));

        $this->assertCount(1, $queries);
        $this->assertSame([$south->id], $this->normal($queries[0]));
    }

    public function test_a_batch_that_links_to_nothing_new_asks_for_nothing(): void
    {
        $north = $this->brand('North');
        $this->pages()->preloadLinks($this->batch($this->parcel(['brand_id' => $north->id])));

        $queries = $this->brandQueries(fn () => $this->pages()->preloadLinks($this->batch($this->parcel(['brand_id' => $north->id]))));

        $this->assertSame([], $queries);
    }

    public function test_a_link_by_another_column_is_loaded_by_that_column_and_only_when_filled_in(): void
    {
        $north = $this->brand('North', 'n-1');
        $parcels = $this->batch(
            $this->parcel(['keeper' => 'n-1']),
            $this->parcel(['keeper' => '']),
            $this->parcel(['keeper' => null]),
            $this->parcel(['keeper' => 'n-1']),
        );

        $queries = $this->brandQueries(fn () => $this->pages()->preloadLinks($parcels));

        $this->assertSame([['n-1']], $queries);
        $this->assertSame($north->id, $this->pages()->linked(Brand::class, 'xml_id', 'n-1')?->getKey());
    }

    public function test_a_value_that_is_a_list_is_not_a_link(): void
    {
        $north = $this->brand('North');
        $gadgets = $this->batch(
            Gadget::query()->create(['name' => 'Lamp', 'brand' => $north->id, 'owner' => [1, 2]]),
            Gadget::query()->create(['name' => 'Fan', 'owner' => [3]]),
        );

        $queries = $this->brandQueries(fn () => $this->pages()->preloadLinks($gadgets));

        $this->assertSame([$north->id], $this->normal($queries[0]));
        $this->assertCount(1, $queries);
    }

    public function test_the_links_to_many_come_with_the_batch_when_the_relation_is_one_of_many(): void
    {
        $north = $this->brand('North');
        $first = Product::query()->create(['name' => 'First', 'code' => 'first']);
        $first->related()->attach(Product::query()->create(['name' => 'Second', 'code' => 'second'])->id);
        $this->parcel()->tags()->attach($north->id);
        $products = $this->batch(...Product::query()->whereKey($first->id)->get()->all());
        $parcels = $this->batch(...Parcel::query()->get()->all());

        $this->pages()->preloadLinks($products);
        $this->pages()->preloadLinks($parcels);

        $this->assertTrue($products->first()?->relationLoaded('related'));
        $this->assertTrue($parcels->first()?->relationLoaded('tags'));
    }

    public function test_a_link_to_many_that_is_no_relation_of_that_kind_is_left_alone(): void
    {
        // "owner" is a belongsTo here, "maker" too, and "parts" is no relation at all.
        $parcel = $this->parcel();
        $gadget = Gadget::query()->create(['name' => 'Lamp']);

        $this->pages()->preloadLinks($this->batch($parcel));
        $this->pages()->preloadLinks($this->batch($gadget));

        $this->assertFalse($parcel->relationLoaded('owner'));
        $this->assertFalse($gadget->relationLoaded('maker'));
        $this->assertFalse($gadget->relationLoaded('parts'));
    }

    public function test_a_batch_of_nothing_loads_nothing(): void
    {
        $queries = $this->brandQueries(fn () => $this->pages()->preloadLinks($this->batch()));

        $this->assertSame([], $queries);
    }

    public function test_what_a_batch_loaded_is_forgotten_on_request(): void
    {
        $north = $this->brand('North');
        $this->pages()->preloadLinks($this->batch($this->parcel(['brand_id' => $north->id])));
        $this->pages()->forgetLinks();

        $queries = $this->brandQueries(fn () => $this->pages()->linked(Brand::class, 'id', $north->id));

        $this->assertCount(1, $queries);
    }

    // the relation behind a link

    public function test_a_link_reads_the_relation_the_card_carries_with_no_query(): void
    {
        $north = $this->brand('North');
        $parcel = $this->parcel(['brand_id' => $north->id]);
        $parcel->setRelation('brand', new Brand(['name' => 'Loaded', 'xml_id' => 'loaded']));

        $queries = $this->brandQueries(fn () => $this->assertSame('Loaded', $this->pageOf($parcel)->blocks()[0]['items'][0]['value']));

        $this->assertSame([], $queries);
    }

    public function test_a_link_without_a_loaded_relation_asks_for_the_record(): void
    {
        $north = $this->brand('North');
        $parcel = $this->parcel(['brand_id' => $north->id]);

        $this->assertSame('North', $this->pageOf($parcel)->blocks()[0]['items'][0]['value']);
    }

    public function test_a_link_by_another_column_ignores_the_relation_that_may_hold_the_wrong_record(): void
    {
        $this->brand('North', 'n-1');
        $parcel = $this->parcel(['maker_id' => 'n-1']);
        $parcel->setRelation('maker', new Brand(['name' => 'Wrong', 'xml_id' => 'wrong']));

        $this->assertSame('North', $this->pageOf($parcel)->blocks()[0]['items'][0]['value']);
    }

    public function test_a_link_by_another_column_and_no_relation_loaded_asks_for_the_record(): void
    {
        $this->brand('North', 'n-1');
        $parcel = $this->parcel(['keeper' => 'n-1']);

        $this->assertSame('North', $this->pageOf($parcel)->blocks()[0]['items'][0]['value']);
    }

    public function test_a_link_with_a_relation_of_the_field_name_asks_for_the_record(): void
    {
        $north = $this->brand('North');
        $product = Product::query()->create(['name' => 'Lamp', 'code' => 'lamp', 'brand' => $north->id]);
        $product->setRelation('brand', new Brand(['name' => 'Wrong', 'xml_id' => 'wrong']));

        $properties = array_column($this->pageOf($product)->blocks()[0]['items'], 'value', 'label');

        $this->assertSame('North', $properties['Brand']);
    }
}
