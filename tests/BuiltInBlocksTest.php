<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Builder\BlockType;
use Atrium\Admin\Builder\BlockTypes;
use Atrium\Admin\Embeds\Embeds;
use Atrium\Admin\Facades\Hook;
use Atrium\Admin\Fields\Field;
use Atrium\Theme\Builder\BlockLinks;
use Atrium\Theme\Builder\Blocks\ButtonBlock;
use Atrium\Theme\Builder\Blocks\GalleryBlock;
use Atrium\Theme\Builder\Blocks\MapBlock;
use Atrium\Theme\Builder\Blocks\PictureBlock;
use Atrium\Theme\Builder\Blocks\TextBlock;
use Atrium\Theme\Builder\LayoutRenderer;
use Atrium\Theme\Tests\Fixtures\LinkedStory;
use Atrium\Theme\Tests\Fixtures\NoteEmbed;
use Atrium\Theme\Tests\Fixtures\Post;
use Atrium\Theme\Tests\Fixtures\ProbeBlock;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The block types that come with the theme: text, picture, gallery, button
 * and map — what they ask for, what they draw and what they refuse to.
 */
final class BuiltInBlocksTest extends TestCase
{
    private const TYPES = [
        'text' => [TextBlock::class, 10],
        'picture' => [PictureBlock::class, 20],
        'gallery' => [GalleryBlock::class, 30],
        'button' => [ButtonBlock::class, 40],
        'map' => [MapBlock::class, 50],
    ];

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('atrium.resources', [LinkedStory::class]);
        $app['config']->set('atrium.public_urls', [
            ...$app['config']->get('atrium.public_urls'),
            LinkedStory::class => ['prefix' => 'stories'],
        ]);
    }

    private function type(string $key): BlockType
    {
        $type = $this->app->make(BlockTypes::class)->get($key);
        $this->assertNotNull($type);

        return $type;
    }

    /**
     * @param  array<string,mixed>  $settings
     * @return array<string,mixed>|null
     */
    private function data(string $key, array $settings): ?array
    {
        return $this->type($key)->data($settings);
    }

    /**
     * A page with one block, as a visitor gets it.
     *
     * @param  array<string,mixed>  $settings
     */
    private function draw(string $key, array $settings): string
    {
        $page = [['id' => 's1', 'columns' => [['id' => 'c1', 'blocks' => [['id' => 'b1', 'type' => $key, 'settings' => $settings]]]]]];

        return $this->app->make(LayoutRenderer::class)->render($page)->toHtml();
    }

    private function css(string $name): string
    {
        return (string) file_get_contents(__DIR__.'/../resources/css/builder/'.$name.'.css');
    }

    private function story(bool $active = true, string $code = 'hello'): LinkedStory
    {
        return LinkedStory::query()->create(['name' => ['en' => 'Hello'], 'code' => ['en' => $code], 'active' => $active]);
    }

    /**
     * @return array<string,mixed>
     */
    private function field(string $type, string $name): array
    {
        foreach ($this->type($type)->fields() as $field) {
            if ($field->toArray()['name'] === $name) {
                return $field->toArray();
            }
        }

        $this->fail("The block \"{$type}\" has no setting \"{$name}\".");
    }

    /** The first complaint about a value of a setting, or null. */
    private function complaint(string $type, string $name, mixed $value): ?string
    {
        $field = array_values(array_filter($this->type($type)->fields(), static fn (Field $field): bool => $field->toArray()['name'] === $name))[0];

        return Validator::make([$name => $value], [$name => $field->getValidationRules()])->errors()->first() ?: null;
    }

    private function images(string $driver = 'template'): void
    {
        config()->set('atrium.cdn.images.driver', $driver);
        config()->set('atrium.cdn.images.template', 'https://img.test/{width}/{path}');
        // The neighbours of every width a block asks for.
        config()->set('atrium.cdn.images.widths', [320, 639, 640, 641, 1279, 1280, 1281, 1920, 1921]);
    }

    public function test_the_theme_offers_its_block_types_in_this_order(): void
    {
        $types = $this->app->make(BlockTypes::class)->all();

        $this->assertSame(array_keys(self::TYPES), array_keys($types));

        foreach (self::TYPES as $key => [$class]) {
            $this->assertInstanceOf($class, $types[$key]);
        }
    }

    /**
     * An app replaces one of the theme's types by registering the same key
     * at the same priority or later; an earlier one is replaced by the theme.
     */
    public function test_each_type_is_registered_at_its_own_priority(): void
    {
        foreach (self::TYPES as $key => [$class, $priority]) {
            Hook::register(BlockTypes::REGISTRY, $key, new ProbeBlock, $priority - 1);

            $this->assertInstanceOf($class, $this->type($key), $key);

            Hook::register(BlockTypes::REGISTRY, $key, new ProbeBlock, $priority);

            $this->assertInstanceOf(ProbeBlock::class, $this->type($key), $key);
        }
    }

    public function test_every_type_has_a_name_in_the_language_of_the_panel(): void
    {
        $labels = fn (): array => array_map(static fn (BlockType $type): string => $type->label(), $this->app->make(BlockTypes::class)->all());

        $this->assertSame(['text' => 'Text', 'picture' => 'Picture', 'gallery' => 'Gallery', 'button' => 'Button', 'map' => 'Map'], $labels());

        $this->app->setLocale('de');

        $this->assertSame(__('atrium-theme::builder.types.picture', [], 'de'), $labels()['picture']);
        $this->assertNotSame('Picture', $labels()['picture']);
    }

    /**
     * @return array<string,array{string,array<string,array{string,string,bool}>,array<int,string>}>
     */
    public static function settingsOfTypes(): array
    {
        return [
            'text' => ['text', ['html' => ['content', 'Text', true]], ['html']],
            'picture' => ['picture', [
                'image' => ['image', 'Picture', true],
                'alt' => ['text', 'Description for screen readers', false],
                'caption' => ['text', 'Caption', false],
                'link' => ['link', 'Link', false],
            ], ['alt', 'caption']],
            'gallery' => ['gallery', ['images' => ['gallery', 'Pictures', true]], []],
            'button' => ['button', [
                'label' => ['text', 'Button text', true],
                'link' => ['link', 'Link', true],
                'style' => ['select', 'Look', false],
                'new_tab' => ['checkbox', 'Open in a new tab', false],
            ], ['label']],
            'map' => ['map', [
                'point' => ['map', 'Place (latitude, longitude)', true],
                'zoom' => ['number', 'Zoom', false],
                'address' => ['text', 'Address', false],
            ], ['address']],
        ];
    }

    /**
     * @param  array<string,array{string,string,bool}>  $expected
     * @param  array<int,string>  $translatable
     */
    #[DataProvider('settingsOfTypes')]
    public function test_a_type_asks_for_its_settings(string $key, array $expected, array $translatable): void
    {
        $fields = [];

        foreach ($this->type($key)->fields() as $field) {
            $field = $field->toArray();
            $fields[$field['name']] = [$field['type'], $field['label'], $field['required']];
        }

        $this->assertSame($expected, $fields);
        $this->assertSame($translatable, $this->type($key)->translatable());
    }

    public function test_the_choices_defaults_and_limits_of_the_settings(): void
    {
        $this->assertSame(['wysiwyg', 'html'], $this->field('text', 'html')['modes']);

        foreach ([['picture', 'alt'], ['picture', 'caption'], ['button', 'label'], ['map', 'address']] as [$type, $name]) {
            $this->assertSame(255, $this->field($type, $name)['maxLength'], "{$type}.{$name}");
            $this->assertNull($this->complaint($type, $name, str_repeat('a', 255)));
            $this->assertNotNull($this->complaint($type, $name, str_repeat('a', 256)));
        }

        $style = $this->field('button', 'style');
        $this->assertSame(['primary' => 'Filled', 'secondary' => 'Outlined'], array_column($style['options'], 'label', 'value'));
        $this->assertSame('primary', $style['default']);
        $this->assertFalse($this->field('button', 'new_tab')['default']);

        $zoom = $this->field('map', 'zoom');
        $this->assertSame([0.0, 19.0, 15], [$zoom['min'], $zoom['max'], $zoom['default']]);
    }

    /**
     * @return array<string,array{mixed,bool}>
     */
    public static function zooms(): array
    {
        return [
            'the whole world' => [0, true],
            'the closest' => [19, true],
            'written as text' => ['12', true],
            'below' => [-1, false],
            'above' => [20, false],
            'a fraction' => [1.5, false],
        ];
    }

    #[DataProvider('zooms')]
    public function test_a_zoom_is_a_whole_number_the_map_has(mixed $zoom, bool $accepted): void
    {
        $this->assertSame($accepted, $this->complaint('map', 'zoom', $zoom) === null);
    }

    /**
     * @return array<string,array{string}>
     */
    public static function places(): array
    {
        return [
            'with a space' => ['48.8584, 2.2945'],
            'without one' => ['48.8584,2.2945'],
            'spaces around' => ["  -33.9 , 151.2 \n"],
            'whole degrees' => ['1,2'],
            'the far corner' => ['90,180'],
            'the other corner' => ['-90,-180'],
        ];
    }

    #[DataProvider('places')]
    public function test_a_place_written_as_latitude_and_longitude_is_taken(string $point): void
    {
        $this->assertNull($this->complaint('map', 'point', $point));
        $this->assertNotNull($this->data('map', ['point' => $point]));
    }

    /**
     * @return array<string,array{mixed}>
     */
    public static function notPlaces(): array
    {
        return [
            'a name' => ['Paris'],
            'one number' => ['48.8584'],
            'three numbers' => ['1,2,3'],
            'latitude too far north' => ['90.1,0'],
            'latitude too far south' => ['-90.1,0'],
            'longitude too far east' => ['0,180.1'],
            'longitude too far west' => ['0,-180.1'],
            'something before' => ['x1,2'],
            'something after' => ['1,2x'],
            'a number' => [48],
            'a pair' => [[48, 2]],
        ];
    }

    #[DataProvider('notPlaces')]
    public function test_what_is_not_a_place_is_refused_and_not_drawn(mixed $point): void
    {
        $complaint = $this->complaint('map', 'point', $point);

        $this->assertNotNull($complaint);
        $this->assertNull($this->data('map', ['point' => $point]));

        if (is_string($point)) {
            $this->assertSame('Write the place as latitude and longitude, for example 48.8584, 2.2945.', $complaint);
        }
    }

    public function test_a_map_needs_a_place(): void
    {
        $this->assertNotNull($this->complaint('map', 'point', ''));
        $this->assertNull($this->data('map', []));
    }

    public function test_a_map_frames_the_place_and_links_to_the_whole_map(): void
    {
        $this->assertSame([
            'src' => 'https://www.openstreetmap.org/export/embed.html?bbox=2.283514%2C48.854786%2C2.305486%2C48.862014&layer=mapnik&marker=48.858400%2C2.294500',
            'href' => 'https://www.openstreetmap.org/?mlat=48.858400&mlon=2.294500#map=15/48.858400/2.294500',
            'address' => 'Champ de Mars',
        ], $this->data('map', ['point' => '48.8584, 2.2945', 'address' => ' Champ de Mars ']));
    }

    public function test_the_frame_of_a_map_stays_on_the_globe(): void
    {
        $this->assertStringContainsString('bbox=106.200000%2C-52.575276%2C180.000000%2C-15.224724&', $this->data('map', ['point' => '-33.9, 151.2', 'zoom' => 3])['src']);
        $this->assertStringContainsString('bbox=-180.000000%2C-90.000000%2C180.000000%2C90.000000&', $this->data('map', ['point' => '0, 0', 'zoom' => 0])['src']);
        $this->assertStringContainsString('bbox=-180.000000%2C', $this->data('map', ['point' => '0, -179', 'zoom' => 3])['src']);
        $this->assertStringContainsString('%2C90.000000&', $this->data('map', ['point' => '89, 0', 'zoom' => 1])['src']);
    }

    /**
     * @return array<string,array{array<string,mixed>,int}>
     */
    public static function drawnZooms(): array
    {
        return [
            'nothing stored' => [[], 15],
            'a word' => [['zoom' => 'near'], 15],
            'written as text' => [['zoom' => '3'], 3],
            'a fraction loses its tail' => [['zoom' => 3.9], 3],
            'the whole world' => [['zoom' => 0], 0],
            'the closest' => [['zoom' => 19], 19],
            'above' => [['zoom' => 25], 19],
            'below' => [['zoom' => -4], 0],
        ];
    }

    /**
     * @param  array<string,mixed>  $settings
     */
    #[DataProvider('drawnZooms')]
    public function test_a_stored_zoom_is_brought_into_the_range_of_the_map(array $settings, int $zoom): void
    {
        $this->assertStringContainsString("#map={$zoom}/1.000000/2.000000", $this->data('map', ['point' => '1,2', ...$settings])['href']);
    }

    public function test_the_map_is_drawn_with_its_address_and_names_its_frame_after_it(): void
    {
        $html = $this->draw('map', ['point' => '1,2', 'address' => ['en' => '<b>Pier 1</b>']]);

        $this->assertStringContainsString('<iframe class="b-map-frame" src="https://www.openstreetmap.org/export/embed.html?bbox=', $html);
        $this->assertStringContainsString('title="&lt;b&gt;Pier 1&lt;/b&gt;" loading="lazy"></iframe>', $html);
        $this->assertStringContainsString('<span class="b-map-address">&lt;b&gt;Pier 1&lt;/b&gt;</span>', $html);
        $this->assertStringContainsString('rel="noopener">Open the map</a>', $html);
        $this->assertStringContainsString($this->css('map'), $html);
    }

    public function test_a_map_without_an_address_still_names_its_frame(): void
    {
        $html = $this->draw('map', ['point' => '1,2']);

        $this->assertStringContainsString('title="Map" loading="lazy"', $html);
        $this->assertStringNotContainsString('<span class="b-map-address">', $html);
    }

    /**
     * @return array<string,array{array<string,mixed>}>
     */
    public static function emptyBlocks(): array
    {
        return [
            'text: nothing' => [['text', []]],
            'text: spaces' => [['text', ['html' => "  \n "]]],
            'text: a list' => [['text', ['html' => ['<p>x</p>']]]],
            'picture: nothing' => [['picture', []]],
            'picture: an empty path' => [['picture', ['image' => ' ']]],
            'gallery: nothing' => [['gallery', []]],
            'gallery: an empty list' => [['gallery', ['images' => []]]],
            'gallery: not a list' => [['gallery', ['images' => '/storage/a.jpg']]],
            'gallery: no usable path' => [['gallery', ['images' => ['', 7]]]],
            'button: nothing' => [['button', []]],
            'button: no text' => [['button', ['link' => ['type' => 'url', 'url' => '/a']]]],
            'button: spaces for text' => [['button', ['label' => '  ', 'link' => ['type' => 'url', 'url' => '/a']]]],
            'button: no link' => [['button', ['label' => 'Go']]],
            'button: a link a browser must not follow' => [['button', ['label' => 'Go', 'link' => ['type' => 'url', 'url' => 'javascript:alert(1)']]]],
        ];
    }

    /**
     * @param  array{string,array<string,mixed>}  $block
     */
    #[DataProvider('emptyBlocks')]
    public function test_a_block_with_nothing_to_show_is_not_drawn(array $block): void
    {
        $this->assertNull($this->data($block[0], $block[1]));
    }

    public function test_a_text_is_drawn_with_the_embeds_put_into_it(): void
    {
        Hook::register(Embeds::REGISTRY, 'note', new NoteEmbed);

        $html = $this->draw('text', ['html' => ['en' => ' <p>Before</p><div data-atrium-embed="note" data-id="4"></div> ']]);

        $this->assertStringContainsString('<div class="content"><p>Before</p><aside>NOTE 4</aside></div>', $html);
        $this->assertStringNotContainsString('data-atrium-embed', $html);
    }

    public function test_the_pictures_inside_a_text_go_through_the_cdn(): void
    {
        $settings = ['html' => '<img src="/storage/a.jpg">'];

        $this->assertSame($settings, $this->data('text', $settings));

        config()->set('atrium.cdn.url', 'https://cdn.test');
        config()->set('atrium.cdn.version', false);

        $this->assertSame(['html' => '<img src="https://cdn.test/storage/a.jpg">'], $this->data('text', $settings));
    }

    public function test_a_picture_without_extras_is_just_the_picture(): void
    {
        $this->assertSame([
            'src' => $this->root().'/storage/a.jpg',
            'srcset' => null,
            'alt' => '',
            'caption' => '',
            'href' => null,
        ], $this->data('picture', ['image' => '/storage/a.jpg']));

        $html = $this->draw('picture', ['image' => '/storage/a.jpg']);

        $this->assertStringContainsString('<img src="'.$this->root().'/storage/a.jpg"  alt="" loading="lazy">', $html);
        $this->assertStringNotContainsString('<a ', $html);
        $this->assertStringNotContainsString('<figcaption>', $html);
        $this->assertStringNotContainsString('srcset', $html);
        $this->assertStringContainsString($this->css('picture'), $html);
    }

    public function test_a_picture_is_drawn_with_its_caption_description_and_link(): void
    {
        $html = $this->draw('picture', [
            'image' => '/storage/a.jpg',
            'alt' => ['en' => ' "A" <pier> '],
            'caption' => ['en' => ' <script>alert(1)</script> '],
            'link' => ['type' => 'url', 'url' => '/about'],
        ]);

        $this->assertStringContainsString('<a href="/about">', $html);
        $this->assertStringContainsString('alt="&quot;A&quot; &lt;pier&gt;"', $html);
        $this->assertStringContainsString('<figcaption>&lt;script&gt;alert(1)&lt;/script&gt;</figcaption>', $html);
        $this->assertStringContainsString('</a>', $html);
        $this->assertStringNotContainsString('<script>alert', $html);
    }

    /**
     * @return array<string,array{array<string,string>,string}>
     */
    public static function linkedNames(): array
    {
        return [
            'its description' => [['alt' => 'Pier', 'caption' => 'The old pier'], 'Pier'],
            'else its caption' => [['caption' => 'The old pier'], 'The old pier'],
            'else where it leads' => [[], '/about'],
        ];
    }

    /**
     * @param  array<string,string>  $settings
     */
    #[DataProvider('linkedNames')]
    public function test_a_linked_picture_always_has_a_name(array $settings, string $alt): void
    {
        $data = $this->data('picture', ['image' => '/storage/a.jpg', 'link' => ['type' => 'url', 'url' => '/about'], ...$settings]);

        $this->assertSame($alt, $data['alt']);
    }

    public function test_a_picture_without_a_link_is_not_named_after_its_caption(): void
    {
        $this->assertSame('', $this->data('picture', ['image' => '/storage/a.jpg', 'caption' => 'The old pier'])['alt']);
    }

    public function test_a_picture_is_resized_by_the_image_driver(): void
    {
        $this->images();

        $data = $this->data('picture', ['image' => '/storage/a.jpg']);

        $this->assertSame('https://img.test/1280/storage/a.jpg', $data['src']);
        $this->assertSame(implode(', ', array_map(
            static fn (int $width): string => "https://img.test/{$width}/storage/a.jpg {$width}w",
            [320, 639, 640, 641, 1279, 1280, 1281, 1920],
        )), $data['srcset']);

        $html = $this->draw('picture', ['image' => '/storage/a.jpg']);

        $this->assertStringContainsString('srcset="'.$data['srcset'].'" sizes="(max-width: 40rem) 100vw, 72rem"', $html);
    }

    public function test_a_gallery_shows_small_copies_that_open_in_full(): void
    {
        $this->images();

        $this->assertSame(['images' => [
            ['url' => $this->root().'/storage/a.jpg', 'thumbnail' => 'https://img.test/640/storage/a.jpg'],
            ['url' => 'https://elsewhere.test/b.png', 'thumbnail' => 'https://img.test/640/b.png'],
        ]], $this->data('gallery', ['images' => ['/storage/a.jpg', '', 'https://elsewhere.test/b.png']]));

        $html = $this->draw('gallery', ['images' => ['/storage/a.jpg', '/storage/c.jpg']]);

        $this->assertStringContainsString('<div class="gallery">', $html);
        $this->assertStringContainsString('<a href="'.$this->root().'/storage/a.jpg"><img src="https://img.test/640/storage/a.jpg" alt="Picture 1" loading="lazy"></a>', $html);
        $this->assertStringContainsString('alt="Picture 2"', $html);
    }

    public function test_a_button_carries_its_text_address_and_look(): void
    {
        $link = ['type' => 'url', 'url' => '/contacts'];

        $this->assertSame(
            ['label' => 'Write to us', 'href' => '/contacts', 'style' => 'primary', 'newTab' => false],
            $this->data('button', ['label' => ' Write to us ', 'link' => $link]),
        );
        $this->assertSame(
            ['label' => 'Go', 'href' => '/contacts', 'style' => 'secondary', 'newTab' => true],
            $this->data('button', ['label' => 'Go', 'link' => $link, 'style' => 'secondary', 'new_tab' => '1']),
        );
        $this->assertSame('primary', $this->data('button', ['label' => 'Go', 'link' => $link, 'style' => 'neon'])['style']);
        $this->assertSame('primary', $this->data('button', ['label' => 'Go', 'link' => $link, 'style' => ['secondary']])['style']);
        $this->assertFalse($this->data('button', ['label' => 'Go', 'link' => $link, 'new_tab' => '0'])['newTab']);
    }

    public function test_a_button_opens_a_new_tab_only_when_asked(): void
    {
        $settings = ['label' => ['en' => '<b>Go</b>'], 'link' => ['type' => 'url', 'url' => '/contacts']];

        $same = $this->draw('button', $settings);
        $new = $this->draw('button', [...$settings, 'new_tab' => true, 'style' => 'secondary']);

        $this->assertStringContainsString('<a class="b-button-link b-button-primary" href="/contacts" >&lt;b&gt;Go&lt;/b&gt;</a>', $same);
        $this->assertStringNotContainsString('target=', $same);
        $this->assertStringContainsString('<a class="b-button-link b-button-secondary" href="/contacts"  target="_blank" rel="noopener" >', $new);
        $this->assertStringContainsString($this->css('button'), $new);
    }

    public function test_only_the_blocks_that_have_styles_of_their_own_bring_files(): void
    {
        $this->assertSame([], $this->type('text')->styles());
        $this->assertSame([], $this->type('gallery')->styles());

        foreach (['picture', 'button', 'map'] as $key) {
            $this->assertSame([realpath(__DIR__."/../resources/css/builder/{$key}.css")], array_map(realpath(...), $this->type($key)->styles()));
        }

        foreach (array_keys(self::TYPES) as $key) {
            $this->assertSame([], $this->type($key)->scripts(), $key);
        }
    }

    private function link(mixed $link): ?string
    {
        return $this->app->make(BlockLinks::class)->url($link);
    }

    /**
     * @return array<string,array{mixed,?string}>
     */
    public static function addresses(): array
    {
        return [
            'a page of the site' => [['type' => 'url', 'url' => '/contacts'], '/contacts'],
            'spaces around' => [['type' => 'url', 'url' => '  /contacts '], '/contacts'],
            'another site' => [['type' => 'url', 'url' => 'https://example.com/a'], 'https://example.com/a'],
            'a script' => [['type' => 'url', 'url' => 'javascript:alert(1)'], null],
            'empty' => [['type' => 'url', 'url' => ''], null],
            'no address' => [['type' => 'url'], null],
            'an address that is a list' => [['type' => 'url', 'url' => ['/contacts']], null],
            'nothing' => [null, null],
            'text' => ['/contacts', null],
            'not a list' => [new \ArrayObject(['type' => 'url', 'url' => '/contacts']), null],
            'no kind' => [['url' => '/contacts'], null],
        ];
    }

    #[DataProvider('addresses')]
    public function test_an_address_is_followed_only_when_a_browser_may(mixed $link, ?string $url): void
    {
        $this->assertSame($url, $this->link($link));
    }

    public function test_a_link_to_a_record_leads_to_its_page(): void
    {
        $story = $this->story();

        $this->assertSame($this->root().'/stories/hello', $this->link(['type' => 'record', 'model' => LinkedStory::class, 'id' => $story->id]));
        $this->assertSame($this->root().'/stories/hello', $this->link(['type' => 'record', 'model' => LinkedStory::class, 'id' => (string) $story->id]));
        // Read as a whole number, as the picker's validation reads it.
        $this->assertSame($this->root().'/stories/hello', $this->link(['type' => 'record', 'model' => LinkedStory::class, 'id' => $story->id + 0.5]));
    }

    public function test_a_link_to_a_hidden_or_missing_record_leads_nowhere(): void
    {
        $hidden = $this->story(active: false);
        $unaddressed = $this->story(code: '');

        foreach ([$hidden->id, $unaddressed->id, $unaddressed->id + 100] as $id) {
            $this->assertNull($this->link(['type' => 'record', 'model' => LinkedStory::class, 'id' => $id]));
        }
    }

    public function test_a_link_may_name_only_a_module_the_picker_offers(): void
    {
        $story = $this->story();
        $id = $story->id;

        // The same row through a class that has pages but is no module.
        $this->assertNotNull(Post::query()->find($id));

        foreach ([
            'a model that is no module' => ['type' => 'record', 'model' => Post::class, 'id' => $id],
            'a class that is not there' => ['type' => 'record', 'model' => 'App\\Nothing', 'id' => $id],
            'a model that is a list' => ['type' => 'record', 'model' => [LinkedStory::class], 'id' => $id],
            'no model' => ['type' => 'record', 'id' => $id],
            'no identifier' => ['type' => 'record', 'model' => LinkedStory::class],
            'an identifier with letters' => ['type' => 'record', 'model' => LinkedStory::class, 'id' => $id.'abc'],
            'another kind' => ['type' => 'file', 'model' => LinkedStory::class, 'id' => $id],
        ] as $case => $link) {
            $this->assertNull($this->link($link), $case);
        }
    }

    public function test_a_button_to_a_record_is_drawn_with_the_records_address(): void
    {
        $story = $this->story();

        $html = $this->draw('button', ['label' => ['en' => 'Read'], 'link' => ['type' => 'record', 'model' => LinkedStory::class, 'id' => $story->id]]);

        $this->assertStringContainsString('href="'.$this->root().'/stories/hello"', $html);
    }
}
