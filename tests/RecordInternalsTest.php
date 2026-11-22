<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Fields\ContentField;
use Atrium\Admin\Fields\FileField;
use Atrium\Admin\Fields\GalleryField;
use Atrium\Admin\Fields\ImageField;
use Atrium\Admin\Fields\MultiSelectField;
use Atrium\Admin\Fields\SelectField;
use Atrium\Admin\Fields\TextareaField;
use Atrium\Admin\Fields\TextField;
use Atrium\Admin\Fields\WysiwygField;
use Atrium\Theme\Pages;
use Atrium\Theme\RecordView;
use Atrium\Theme\Tests\Fixtures\Product;
use Atrium\Theme\Tests\Fixtures\Relic;
use Atrium\Theme\Tests\Fixtures\SeoRelic;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * A record as the page works it out from the fields of its form: the
 * texts for search engines, which picture and text come first, and what
 * each kind of field makes of its value.
 */
final class RecordInternalsTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $rules = $app['config']->get('atrium.public_urls');
        $rules[Relic::class] = ['prefix' => 'relics', 'tree' => Relic::class, 'parent' => 'category_id'];
        $rules[SeoRelic::class] = ['prefix' => 'seo-relics'];
        $app['config']->set('atrium.public_urls', $rules);
    }

    protected function tearDown(): void
    {
        Relic::$fields = [];
        SeoRelic::$title = SeoRelic::$description = SeoRelic::$keywords = SeoRelic::$image = null;

        parent::tearDown();
    }

    /**
     * @param  array<string,mixed>  $attributes
     * @param  array<int|string,mixed>  $fields
     */
    private function relic(array $attributes, array $fields = [], string $class = Relic::class): RecordView
    {
        Relic::$fields = $fields;

        return new RecordView(new $class($attributes), app(Pages::class));
    }

    /**
     * @param  array<string,mixed>  $attributes
     * @param  array<int|string,mixed>  $fields
     * @return array<int,array<string,mixed>>
     */
    private function blocks(array $attributes, array $fields): array
    {
        return $this->relic($attributes, $fields)->blocks();
    }

    /**
     * @return array<string,array{0:?string,1:string}>
     */
    public static function seoTitles(): array
    {
        return [
            'the one of the module' => ['Custom title', 'Custom title'],
            'an empty one falls back to the name' => ['', 'Lamp'],
            'none falls back to the name' => [null, 'Lamp'],
        ];
    }

    #[DataProvider('seoTitles')]
    public function test_the_seo_title_is_the_modules_own_or_the_name(?string $own, string $shown): void
    {
        SeoRelic::$title = $own;

        $this->assertSame($shown, $this->relic(['name' => 'Lamp'], class: SeoRelic::class)->seoTitle());
    }

    public function test_a_module_without_seo_texts_has_the_name_for_a_title_and_nothing_for_the_rest(): void
    {
        $view = $this->relic(['name' => 'Lamp', 'preview_text' => 'About the lamp']);

        $this->assertSame('Lamp', $view->seoTitle());
        $this->assertSame('About the lamp', $view->seoDescription());
        $this->assertNull($view->seoKeywords());
    }

    public function test_the_seo_keywords_are_the_modules_own(): void
    {
        SeoRelic::$keywords = 'lamp, desk';

        $this->assertSame('lamp, desk', $this->relic([], class: SeoRelic::class)->seoKeywords());
    }

    /**
     * @return array<string,array{0:?string,1:string}>
     */
    public static function seoDescriptions(): array
    {
        return [
            'the one of the module' => ['Own text', 'Own text'],
            'an empty one is made of the page' => ['', 'The teaser'],
            'none is made of the page' => [null, 'The teaser'],
        ];
    }

    #[DataProvider('seoDescriptions')]
    public function test_the_seo_description_is_the_modules_own_or_made_of_the_page(?string $own, string $shown): void
    {
        SeoRelic::$description = $own;

        $view = $this->relic(['preview_text' => 'The teaser', 'detail_text' => 'The body'], class: SeoRelic::class);

        $this->assertSame($shown, $view->seoDescription());
    }

    public function test_the_description_made_of_the_page_takes_the_body_when_there_is_no_teaser(): void
    {
        $this->assertSame('The body', $this->relic(['detail_text' => 'The body'])->seoDescription());
        $this->assertSame('', $this->relic([])->seoDescription());
    }

    public function test_the_description_made_of_the_page_is_plain_text_on_one_line(): void
    {
        $view = $this->relic(['preview_text' => "<p> It&#039;s &amp; <b>fine</b>\n\n  here </p>"]);

        $this->assertSame("It's & fine here", $view->seoDescription());
    }

    public function test_the_description_made_of_the_page_stops_at_three_hundred_characters(): void
    {
        $exact = str_repeat('a ', 149).'bb';
        $over = str_repeat('a ', 149).'bbb';

        $this->assertSame(300, mb_strlen($exact));
        $this->assertSame($exact, $this->relic(['preview_text' => $exact])->seoDescription());
        $this->assertSame(str_repeat('a ', 148).'a…', $this->relic(['preview_text' => $over])->seoDescription());
    }

    /**
     * @return array<string,array{0:mixed,1:?string}>
     */
    public static function ogImages(): array
    {
        return [
            'the one of the module' => ['https://cdn.example.com/og.png', 'https://cdn.example.com/og.png'],
            'an empty one falls back to the picture' => ['', 'DETAIL'],
            'none falls back to the picture' => [null, 'DETAIL'],
            'not a text falls back to the picture' => [['url' => 'x'], 'DETAIL'],
        ];
    }

    #[DataProvider('ogImages')]
    public function test_the_og_image_is_the_modules_own_or_the_picture_of_the_page(mixed $own, ?string $shown): void
    {
        SeoRelic::$image = $own;

        $view = $this->relic(['detail_picture' => '/storage/detail.jpg'], class: SeoRelic::class);

        $this->assertSame($shown === 'DETAIL' ? $this->root().'/storage/detail.jpg' : $shown, $view->ogImage());
    }

    public function test_a_module_without_an_og_image_has_the_picture_of_the_page(): void
    {
        $this->assertSame($this->root().'/storage/detail.jpg', $this->relic(['detail_picture' => '/storage/detail.jpg'])->ogImage());
    }

    /**
     * @return array<string,array{0:array<string,string>,1:string,2:string}>
     */
    public static function pictures(): array
    {
        $all = ['picture' => '/p.jpg', 'preview_picture' => '/pp.jpg', 'detail_picture' => '/dp.jpg'];

        return [
            'all three' => [$all, '/dp.jpg', '/pp.jpg'],
            'the plain one and the preview' => [['picture' => '/p.jpg', 'preview_picture' => '/pp.jpg'], '/p.jpg', '/pp.jpg'],
            'the plain one and the detail' => [['picture' => '/p.jpg', 'detail_picture' => '/dp.jpg'], '/dp.jpg', '/p.jpg'],
            'the preview and the detail' => [['preview_picture' => '/pp.jpg', 'detail_picture' => '/dp.jpg'], '/dp.jpg', '/pp.jpg'],
            'the preview only' => [['preview_picture' => '/pp.jpg'], '/pp.jpg', '/pp.jpg'],
            'the detail only' => [['detail_picture' => '/dp.jpg'], '/dp.jpg', '/dp.jpg'],
            'none' => [[], '', ''],
        ];
    }

    /**
     * @param  array<string,string>  $attributes
     */
    #[DataProvider('pictures')]
    public function test_the_big_and_the_small_picture_come_in_their_own_order(array $attributes, string $image, string $thumbnail): void
    {
        $view = $this->relic($attributes);

        $this->assertSame($image === '' ? null : $this->root().$image, $view->image());
        $this->assertSame($thumbnail === '' ? null : $this->root().$thumbnail, $view->thumbnail());
    }

    /**
     * @return array<string,array{0:array<string,string>,1:?string}>
     */
    public static function bodies(): array
    {
        return [
            'all three' => [['detail_text' => 'Detail', 'preview_text' => 'Preview', 'description' => 'Description'], 'Detail'],
            'the preview and the description' => [['preview_text' => 'Preview', 'description' => 'Description'], 'Preview'],
            'the description only' => [['description' => 'Description'], 'Description'],
            'none' => [[], null],
        ];
    }

    /**
     * @param  array<string,string>  $attributes
     */
    #[DataProvider('bodies')]
    public function test_the_body_is_the_detail_then_the_preview_then_the_description(array $attributes, ?string $body): void
    {
        $this->assertSame($body, $this->relic($attributes)->body()?->toHtml());
    }

    public function test_a_text_of_blanks_or_tags_with_nothing_in_them_is_no_text(): void
    {
        $this->assertNull($this->relic(['detail_text' => "  \n "])->body());
        $this->assertNull($this->relic(['detail_text' => '<p> </p>'])->body());
        $this->assertSame('<img src="/a.jpg">', $this->relic(['detail_text' => '<img src="/a.jpg">'])->body()?->toHtml());
    }

    public function test_the_fields_of_the_seo_tab_and_the_meta_ones_are_not_for_readers(): void
    {
        $blocks = $this->blocks(
            ['seo_note' => 'In the SEO tab', 'meta_note' => 'A meta field', 'shown' => 'Shown'],
            [
                TextField::make('seo_note')->label('Seo note')->tab('seo'),
                TextField::make('meta_note')->label('Meta note')->tab('params'),
                TextField::make('shown')->label('Shown')->tab('params'),
            ],
        );

        $this->assertSame([['kind' => 'properties', 'items' => [['code' => 'shown', 'label' => 'Shown', 'value' => 'Shown', 'url' => null, 'links' => []]]]], $blocks);
    }

    public function test_what_is_empty_is_not_shown_and_what_is_not_a_field_is_not_a_field(): void
    {
        $blocks = $this->blocks(
            ['empty' => '', 'none' => null, 'list' => [], 'note' => 'Hello'],
            [
                'not a field',
                TextareaField::make('empty')->label('Empty'),
                TextareaField::make('none')->label('None'),
                TextareaField::make('list')->label('List'),
                TextareaField::make('note')->label('Note'),
            ],
        );

        $this->assertSame([['kind' => 'html', 'label' => 'Note', 'html' => $blocks[0]['html'] ?? null]], $blocks);
        $this->assertSame('Hello', (string) $blocks[0]['html']);
    }

    public function test_the_parent_column_of_the_rule_is_not_a_property(): void
    {
        $fields = [TextField::make('category_id')->label('Category'), TextField::make('other_id')->label('Other'), TextField::make('sort')->label('Sort')];
        $attributes = ['category_id' => 5, 'other_id' => 6, 'sort' => 7];

        $this->assertSame(['Other'], array_column($this->blocks($attributes, $fields)[0]['items'], 'label'));

        $post = new RecordView(new SeoRelic($attributes), app(Pages::class));
        Relic::$fields = $fields;

        $this->assertSame(['Category', 'Other'], array_column($post->blocks()[0]['items'], 'label'));
    }

    public function test_pictures_and_texts_of_a_form_are_shown_by_their_kind(): void
    {
        $blocks = $this->blocks(
            ['photo' => '/storage/a.jpg', 'story' => '<p>Story</p>', 'rich' => '<p>Rich</p>'],
            [
                ImageField::make('photo')->label('Photo'),
                ContentField::make('story')->label('Story'),
                WysiwygField::make('rich')->label('Rich'),
            ],
        );

        $this->assertSame(['images', 'html', 'html'], array_column($blocks, 'kind'));
        $this->assertSame([$this->root().'/storage/a.jpg'], $blocks[0]['images']);
        $this->assertSame('<p>Rich</p>', (string) $blocks[2]['html']);
    }

    public function test_the_pictures_of_a_gallery_are_numbered_from_zero_whatever_is_left_out(): void
    {
        $blocks = $this->blocks(['shots' => ['/a.jpg', '', '/b.jpg']], [GalleryField::make('shots')->label('Shots')]);

        $this->assertSame([$this->root().'/a.jpg', $this->root().'/b.jpg'], $blocks[0]['images']);
    }

    /**
     * @return array<string,array{0:mixed,1:array<int,string>}>
     */
    public static function galleries(): array
    {
        return [
            'a list in JSON' => ['["/a.jpg","/b.jpg"]', ['/a.jpg', '/b.jpg']],
            'a list in JSON after a blank' => [' ["/a.jpg"]', ['/a.jpg']],
            'a text that is no JSON' => ['/a.jpg', ['/a.jpg']],
            'a JSON that is no list' => ['[nonsense', ['/storage/[nonsense']],
            'a file as a map with a path' => [['path' => '/a.jpg'], ['/a.jpg']],
            'a file as a map with a url' => [['url' => '/a.jpg'], ['/a.jpg']],
            'a file as a map with both' => [['path' => '/a.jpg', 'url' => '/b.jpg'], ['/b.jpg']],
            'a map of files' => [['x' => '/a.jpg', 'y' => '/b.jpg'], ['/a.jpg', '/b.jpg']],
            'a list of maps' => [[['path' => '/a.jpg'], ['url' => '/b.jpg']], ['/a.jpg', '/b.jpg']],
        ];
    }

    /**
     * @param  array<int,string>  $paths
     */
    #[DataProvider('galleries')]
    public function test_a_value_of_a_gallery_may_be_one_file_or_many_in_any_of_the_ways_the_admin_keeps_them(mixed $value, array $paths): void
    {
        $blocks = $this->blocks(['shots' => $value], [GalleryField::make('shots')->label('Shots')]);

        $this->assertSame(array_map(fn (string $path): string => $this->root().$path, $paths), $blocks[0]['images']);
    }

    public function test_files_are_named_by_the_admin_or_by_their_address(): void
    {
        $blocks = $this->blocks(
            ['docs' => [['url' => '/files/guide.pdf', 'name' => 'The guide'], '/files/a%20b.pdf', ['url' => '/files/c.pdf'], ['url' => '/files/d.pdf', 'name' => 5]]],
            [FileField::make('docs')->label('Docs')],
        );

        $this->assertSame('files', $blocks[0]['kind']);
        $this->assertSame([
            ['name' => 'The guide', 'url' => $this->root().'/files/guide.pdf'],
            ['name' => 'a b.pdf', 'url' => $this->root().'/files/a%20b.pdf'],
            ['name' => 'c.pdf', 'url' => $this->root().'/files/c.pdf'],
            ['name' => '5', 'url' => $this->root().'/files/d.pdf'],
        ], $blocks[0]['files']);
    }

    public function test_a_file_that_leads_nowhere_is_left_out(): void
    {
        $this->assertSame([], $this->blocks(['docs' => ['  ', null]], [FileField::make('docs')->label('Docs')]));
    }

    public function test_options_are_shown_by_their_labels(): void
    {
        $options = [
            ['value' => 1.5, 'label' => 'Half'],
            ['value' => 1, 'label' => 'One'],
            ['id' => 3, 'name' => 'No value here'],
            ['value' => 'plain'],
            ['value' => 7],
        ];
        $fields = [SelectField::make('size')->label('Size')->options($options), MultiSelectField::make('sizes')->label('Sizes')->options($options)];

        $items = $this->blocks(['size' => 1.5, 'sizes' => [1, 1.5, 'plain', 7, 'unknown']], $fields)[0]['items'];

        $this->assertSame('Half', $items[0]['value']);
        $this->assertSame('One, Half, plain, 7, unknown', $items[1]['value']);
    }

    public function test_a_list_of_values_is_one_line_without_what_has_nothing_to_show(): void
    {
        $items = $this->blocks(['tags' => ['a', true, '', 'b'], 'nested' => [['a', true], 'b']], [TextField::make('tags')->label('Tags'), TextField::make('nested')->label('Nested')])[0]['items'];

        $this->assertSame('a, b', $items[0]['value']);
        $this->assertSame('a, b', $items[1]['value']);
    }

    public function test_a_flag_alone_is_nothing_to_read(): void
    {
        $this->assertSame([], $this->blocks(['flag' => true], [TextField::make('flag')->label('Flag')]));
    }

    public function test_a_second_look_at_the_blocks_changes_nothing(): void
    {
        $view = $this->relic(['note' => 'Hi'], [TextField::make('note')->label('Note')]);
        $first = $view->blocks();
        Relic::$fields = [];

        $this->assertSame($first, $view->blocks());
    }

    public function test_the_related_records_are_links_unless_they_are_hidden_or_have_no_name(): void
    {
        $main = Product::query()->create(['name' => 'Main', 'code' => 'main']);
        $hidden = Product::query()->create(['name' => 'Hidden', 'code' => 'hidden', 'active' => false]);
        $shown = Product::query()->create(['name' => 'Shown', 'code' => 'shown']);
        $nameless = Product::query()->create(['name' => '', 'code' => 'nameless']);
        $main->related()->attach([$hidden->id, $shown->id, $nameless->id]);

        $blocks = (new RecordView($main, app(Pages::class)))->blocks();
        $related = collect($blocks)->pluck('items')->flatten(1)->firstWhere('label', 'Related');

        $this->assertSame('Hidden, Shown', $related['value']);
        $this->assertNull($related['url']);
        $this->assertSame([
            ['value' => 'Hidden', 'url' => null],
            ['value' => 'Shown', 'url' => $this->root().'/catalog/shown'],
        ], $related['links']);
    }

    public function test_related_records_without_names_make_no_block(): void
    {
        $main = Product::query()->create(['name' => 'Main', 'code' => 'main']);
        $main->related()->attach(Product::query()->create(['name' => '', 'code' => 'nameless'])->id);

        $labels = collect((new RecordView($main, app(Pages::class)))->blocks())->pluck('items')->flatten(1)->pluck('label');

        $this->assertNotContains('Related', $labels->all());
    }
}
