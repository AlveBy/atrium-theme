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
use Illuminate\Support\HtmlString;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The properties of a record as a page shows them: what is printed, how
 * and what is left out because there is nothing to show.
 */
final class RecordViewTest extends TestCase
{
    /**
     * @return array<string,array{0:array<string,mixed>,1:string}>
     */
    public static function titles(): array
    {
        return [
            'the name' => [['name' => 'Lamp', 'title' => 'Desk lamp'], 'Lamp'],
            'the title when there is no name' => [['name' => '', 'title' => 'Desk lamp'], 'Desk lamp'],
            'neither: the number of the record' => [['name' => null, 'title' => null], '#{id}'],
        ];
    }

    /**
     * @param  array<string,mixed>  $attributes
     */
    #[DataProvider('titles')]
    public function test_the_title_of_a_record(array $attributes, string $title): void
    {
        $gadget = Gadget::query()->create($attributes);

        $this->assertSame(str_replace('{id}', (string) $gadget->id, $title), $this->viewOf($gadget)->title());
    }

    public function test_notes_keep_their_line_breaks_and_are_escaped(): void
    {
        $block = $this->block(['notes' => "Line one\n<b>two</b>"], 'Notes');

        $this->assertSame('html', $block['kind'] ?? null);
        $this->assertInstanceOf(HtmlString::class, $block['html']);
        $this->assertSame("Line one<br />\n&lt;b&gt;two&lt;/b&gt;", (string) $block['html']);
    }

    /**
     * @return array<string,array{0:string,1:string,2:string}>
     */
    public static function dates(): array
    {
        return [
            'a date' => ['released_on', '2026-03-04', 'March 4, 2026'],
            'a date and time' => ['released_at', '2026-03-04 10:30:00', 'March 4, 2026 10:30 AM'],
            'a date that is not one' => ['released_on', 'soon', 'soon'],
            'a date and time that is not one' => ['released_at', 'some day', 'some day'],
        ];
    }

    #[DataProvider('dates')]
    public function test_dates_kept_as_text_are_written_for_people(string $column, string $value, string $shown): void
    {
        $this->assertSame([$shown], array_column($this->properties([$column => $value]), 'value'));
    }

    public function test_dates_follow_the_language_of_the_page(): void
    {
        app()->setLocale('de');

        $this->assertSame(['4. März 2026'], array_column($this->properties(['released_on' => '2026-03-04']), 'value'));
    }

    /**
     * @return array<string,array{0:mixed,1:list<string>}>
     */
    public static function sizes(): array
    {
        return [
            'a list' => [['S', 'M'], ['S, M']],
            'lists inside a list' => [[['S', 'M'], 'L'], ['S, M, L']],
            'empty values inside are left out' => [[['S', '', 'M'], ''], ['S, M']],
            'only empty values: nothing' => [['', ''], []],
            'yes and no mean nothing to a reader' => [[true, false], []],
        ];
    }

    /**
     * @param  list<string>  $shown
     */
    #[DataProvider('sizes')]
    public function test_lists_are_written_as_one_line(mixed $sizes, array $shown): void
    {
        $this->assertSame($shown, array_column($this->properties(['sizes' => $sizes]), 'value'));
    }

    /**
     * @return array<string,array{0:mixed,1:list<string>}>
     */
    public static function shots(): array
    {
        return [
            'a list of files' => [['a.jpg', 'https://cdn.test/b.jpg'], ['/storage/a.jpg', 'https://cdn.test/b.jpg']],
            'one uploaded file' => [['path' => 'c.jpg', 'name' => 'C'], ['/storage/c.jpg']],
            'a list of uploaded files' => [[['path' => 'c.jpg'], ['url' => '/upload/d.jpg']], ['/storage/c.jpg', '/upload/d.jpg']],
        ];
    }

    /**
     * @param  list<string>  $images
     */
    #[DataProvider('shots')]
    public function test_a_gallery_shows_every_picture(mixed $shots, array $images): void
    {
        $block = $this->block(['shots' => $shots], 'Shots');

        $this->assertSame('images', $block['kind'] ?? null);
        $this->assertSame(array_map(fn (string $url): string => str_starts_with($url, '/') ? url($url) : $url, $images), $block['images']);
    }

    public function test_a_gallery_without_a_picture_to_show_is_left_out(): void
    {
        $gadget = Gadget::query()->create(['name' => 'Lamp', 'shots' => ['', '  ', ['name' => 'no file']]]);

        $this->assertSame([], $this->viewOf($gadget)->blocks());
    }

    public function test_a_linked_record_is_shown_by_its_name(): void
    {
        $brand = Brand::query()->create(['name' => 'Acme', 'xml_id' => 'acme']);

        $this->assertSame(
            [['label' => 'Brand', 'value' => 'Acme', 'url' => null, 'links' => []]],
            $this->properties(['brand' => $brand->id]),
        );
    }

    /**
     * @return array<string,array{0:array<string,mixed>}>
     */
    public static function deadLinks(): array
    {
        return [
            'a record that is gone' => [['brand' => 999]],
            'a value that is a list' => [['owner' => [1, 2]]],
            'a field that names no module' => [['keeper' => 1]],
        ];
    }

    /**
     * @param  array<string,mixed>  $attributes
     */
    #[DataProvider('deadLinks')]
    public function test_a_link_to_nothing_is_left_out(array $attributes): void
    {
        Brand::query()->create(['name' => 'Acme', 'xml_id' => 'acme']);

        $this->assertSame([], $this->properties($attributes));
    }

    public function test_a_linked_record_without_a_name_is_left_out(): void
    {
        $brand = Brand::query()->create(['name' => '', 'xml_id' => 'acme']);

        $this->assertSame([], $this->properties(['brand' => $brand->id]));
    }

    public function test_links_to_many_need_a_relation_of_that_kind(): void
    {
        // "maker" is a belongsTo and "parts" is no relation at all.
        $brand = Brand::query()->create(['name' => 'Acme', 'xml_id' => 'acme']);

        $this->assertSame(['Brand'], array_column($this->properties(['brand' => $brand->id]), 'label'));
    }

    public function test_the_blocks_are_the_same_each_time_they_are_asked_for(): void
    {
        $view = $this->viewOf(Gadget::query()->create(['name' => 'Lamp', 'notes' => 'Bright', 'sizes' => ['S']]));

        $first = $view->blocks();

        $this->assertCount(2, $first);
        $this->assertEquals($first, $view->blocks());
    }

    private function viewOf(Gadget $gadget): RecordView
    {
        return new RecordView($gadget, $this->app->make(Pages::class));
    }

    /**
     * @param  array<string,mixed>  $attributes
     * @return array<string,mixed>
     */
    private function block(array $attributes, string $label): array
    {
        foreach ($this->viewOf(Gadget::query()->create(['name' => 'Lamp', ...$attributes]))->blocks() as $block) {
            if (($block['label'] ?? null) === $label) {
                return $block;
            }
        }

        $this->fail("No block {$label}.");
    }

    /**
     * @param  array<string,mixed>  $attributes
     * @return array<int,array<string,mixed>>
     */
    private function properties(array $attributes): array
    {
        $blocks = $this->viewOf(Gadget::query()->create(['name' => 'Lamp', ...$attributes]))->blocks();
        $properties = array_values(array_filter($blocks, fn (array $block): bool => $block['kind'] === 'properties'));

        $this->assertLessThanOrEqual(1, count($properties));
        $this->assertCount(count($properties), $blocks, 'Only properties were expected.');

        return $properties[0]['items'] ?? [];
    }
}
