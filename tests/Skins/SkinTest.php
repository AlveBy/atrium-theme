<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Skins;

use Atrium\Theme\Pages;
use Atrium\Theme\RecordView;
use Atrium\Theme\Skins\HomeContent;
use Atrium\Theme\Skins\Skin;
use Atrium\Theme\Skins\Skins;
use Atrium\Theme\Tests\TestCase;
use Atrium\Theme\ThemeServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * One skin on its own: where its files, views and texts come from, its
 * fonts, the list of skins and the prices it prints.
 */
final class SkinTest extends TestCase
{
    private string $folder;

    protected function setUp(): void
    {
        parent::setUp();

        // A skin of another package: its own CSS, picture, views and texts.
        $this->folder = sys_get_temp_dir().'/atrium-own-skin-'.uniqid();
        $files = new Filesystem;
        $files->ensureDirectoryExists($this->folder.'/skins/law');
        $files->ensureDirectoryExists($this->folder.'/lang/en');
        file_put_contents($this->folder.'/skins/law.css', '.law-only{}');
        file_put_contents($this->folder.'/skins/law/scales.svg', "\n  <svg viewBox=\"0 0 1 1\"><svg x=\"0\"></svg></svg>\n");
        file_put_contents($this->folder.'/lang/en/skins.php', "<?php return ['law' => ['title' => 'Counsel for :who']];");
        $this->app['translator']->addNamespace('law-package', $this->folder.'/lang');
    }

    protected function tearDown(): void
    {
        (new Filesystem)->deleteDirectory($this->folder);

        parent::tearDown();
    }

    public function test_a_skin_of_another_package_reads_its_own_files_views_and_texts(): void
    {
        $skin = new Skin('law', ['manrope'], 'light', $this->folder, 'law-package');

        $styles = $skin->styles();
        $this->assertStringContainsString('.law-only{}', $styles);
        $this->assertStringContainsString('What every skin shares', $styles);
        $this->assertStringNotContainsString(':root[data-skin="travel"]', $styles);

        $this->assertSame('law-package::skins.law.home', $skin->view('home'));
        $this->assertSame('Counsel for families', $skin->text('title', ['who' => 'families']));
        $this->assertTrue($skin->hasText('title'));
        $this->assertFalse($skin->hasText('missing'));
        $this->assertFalse((new Skin('law', []))->hasText('title'));

        // Only the outer <svg> is hidden from screen readers.
        $this->assertSame(
            '<svg aria-hidden="true" focusable="false" viewBox="0 0 1 1"><svg x="0"></svg></svg>',
            (string) $skin->illustration('scales'),
        );
    }

    public function test_the_theme_s_own_skins_read_the_theme_s_files(): void
    {
        $skin = new Skin('travel', []);

        $this->assertNull($skin->path);
        $this->assertSame('atrium-theme', $skin->views);
        $this->assertSame('light', $skin->scheme);
        $this->assertSame('atrium-theme::skins.travel.footer', $skin->view('footer'));
        $this->assertStringContainsString(':root[data-skin="travel"]', $skin->styles());
        $this->assertSame('travel agency', $skin->text('tagline'));
        $this->assertTrue($skin->hasText('modules.catalog'));
        $this->assertFalse($skin->hasText('modules.pages'));
        $this->assertStringStartsWith('<svg aria-hidden="true" focusable="false" ', (string) $skin->illustration('travel-coast'));
        $this->assertSame(dirname(__DIR__, 2).'/resources/fonts', Skin::fontsDirectory());
    }

    public function test_only_published_font_files_get_a_font_face_and_unknown_fonts_none(): void
    {
        $skin = new Skin('travel', ['nonexistent', 'cormorant-garamond']);
        $this->assertStringNotContainsString('@font-face', $skin->styles());

        $public = sys_get_temp_dir().'/atrium-fonts-'.uniqid();
        $files = new Filesystem;
        $files->copyDirectory(Skin::fontsDirectory(), $public.'/'.Skin::FONTS_PATH);
        $files->delete($public.'/'.Skin::FONTS_PATH.'/cormorant-garamond-latin-ext-wght-italic.woff2');
        $this->app->usePublicPath($public);

        try {
            $styles = $skin->styles();
        } finally {
            $files->deleteDirectory($public);
        }

        preg_match_all('/@font-face\{[^}]*\}/', $styles, $faces);

        // The fonts first, then what every skin shares, then the skin's own.
        $shared = strpos($styles, 'What every skin shares');
        $this->assertLessThan($shared, strrpos($styles, '@font-face'));
        $this->assertLessThan(strpos($styles, ':root[data-skin="travel"]'), $shared);

        $url = asset(Skin::FONTS_PATH.'/');
        $this->assertSame([
            "@font-face{font-family:'Cormorant Garamond';font-style:normal;font-display:swap;font-weight:100 900;src:url({$url}/cormorant-garamond-latin-wght-normal.woff2) format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}",
            "@font-face{font-family:'Cormorant Garamond';font-style:normal;font-display:swap;font-weight:100 900;src:url({$url}/cormorant-garamond-latin-ext-wght-normal.woff2) format('woff2');unicode-range:U+0100-02BA,U+02BD-02C5,U+02C7-02CC,U+02CE-02D7,U+02DD-02FF,U+0304,U+0308,U+0329,U+1D00-1DBF,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20C0,U+2113,U+2C60-2C7F,U+A720-A7FF}",
            "@font-face{font-family:'Cormorant Garamond';font-style:normal;font-display:swap;font-weight:100 900;src:url({$url}/cormorant-garamond-cyrillic-wght-normal.woff2) format('woff2');unicode-range:U+0301,U+0400-045F,U+0490-0491,U+04B0-04B1,U+2116}",
            "@font-face{font-family:'Cormorant Garamond';font-style:normal;font-display:swap;font-weight:100 900;src:url({$url}/cormorant-garamond-cyrillic-ext-wght-normal.woff2) format('woff2');unicode-range:U+0460-052F,U+1C80-1C8A,U+20B4,U+2DE0-2DFF,U+A640-A69F,U+FE2E-FE2F}",
            "@font-face{font-family:'Cormorant Garamond';font-style:italic;font-display:swap;font-weight:100 900;src:url({$url}/cormorant-garamond-latin-wght-italic.woff2) format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}",
            "@font-face{font-family:'Cormorant Garamond';font-style:italic;font-display:swap;font-weight:100 900;src:url({$url}/cormorant-garamond-cyrillic-wght-italic.woff2) format('woff2');unicode-range:U+0301,U+0400-045F,U+0490-0491,U+04B0-04B1,U+2116}",
            "@font-face{font-family:'Cormorant Garamond';font-style:italic;font-display:swap;font-weight:100 900;src:url({$url}/cormorant-garamond-cyrillic-ext-wght-italic.woff2) format('woff2');unicode-range:U+0460-052F,U+1C80-1C8A,U+20B4,U+2DE0-2DFF,U+A640-A69F,U+FE2E-FE2F}",
        ], $faces[0]);
    }

    public function test_every_font_file_a_skin_names_is_in_the_package(): void
    {
        $missing = [];

        foreach (app(Skins::class)->all() as $skin) {
            foreach ($skin->fonts as $font) {
                if (glob(Skin::fontsDirectory()."/{$font}-latin-wght-normal.woff2") === []) {
                    $missing[] = $font;
                }
            }
        }

        $this->assertSame([], $missing);
    }

    public function test_each_skin_has_its_fonts_and_opening_colours(): void
    {
        $skins = array_map(fn (Skin $skin): array => [$skin->fonts, $skin->scheme], app(Skins::class)->all());

        $this->assertSame([
            'travel' => [['unbounded', 'manrope'], 'light'],
            'restaurant' => [['cormorant-garamond', 'inter'], 'dark'],
            'clinic' => [['manrope'], 'light'],
            'shop' => [['space-grotesk', 'inter', 'jetbrains-mono'], 'light'],
        ], $skins);
    }

    public function test_the_list_of_skins_is_one_per_application_and_the_fonts_are_published(): void
    {
        $this->assertSame(app(Skins::class), app(Skins::class));

        $fonts = [Skin::fontsDirectory() => public_path(Skin::FONTS_PATH)];
        $this->assertSame($fonts, ServiceProvider::pathsToPublish(ThemeServiceProvider::class, 'atrium-theme-assets'));
        $this->assertSame($fonts, array_intersect_key(ServiceProvider::pathsToPublish(ThemeServiceProvider::class, 'atrium-assets'), $fonts));
    }

    public function test_another_package_adds_a_skin_or_replaces_one(): void
    {
        $skins = new Skins;
        $law = new Skin('law', []);
        $skins->register($law);

        $this->assertSame(['travel', 'restaurant', 'clinic', 'shop', 'law'], array_keys($skins->all()));
        $this->assertSame($law, $skins->find('law'));

        $travel = new Skin('travel', [], 'dark');
        $skins->register($travel);
        $this->assertSame($travel, $skins->find('travel'));
        $this->assertCount(5, $skins->all());

        $this->assertNull($skins->find(null));
        $this->assertNull($skins->find('nope'));
        $this->assertSame('dark', $skins->find('restaurant')?->scheme);
    }

    public function test_the_current_skin_is_the_configured_one(): void
    {
        $skins = new Skins;

        config(['atrium-theme.skin' => ' clinic ']);
        $this->assertSame('clinic', $skins->current()?->name);

        config(['atrium-theme.skin' => 'nope']);
        $this->assertNull($skins->current());

        config(['atrium-theme.skin' => null]);
        $this->assertNull($skins->current());
    }

    /**
     * @return array<string,array{string,string,string,string}>
     */
    public static function prices(): array
    {
        return [
            'English, whole' => ['en', 'travel', '1250', 'from €1,250'],
            'English, cents' => ['en', 'shop', '19.5', '€19.50'],
            'German' => ['de', 'travel', '1250', 'ab 1.250 €'],
            'German, cents' => ['de', 'restaurant', '7.25', '7,25 €'],
            'Russian' => ['ru', 'clinic', '38000', 'от 38 000 €'],
            'Japanese' => ['ja', 'shop', '1250', '€1,250'],
        ];
    }

    #[DataProvider('prices')]
    public function test_prices_follow_the_page_s_language(string $locale, string $skin, string $price, string $expected): void
    {
        app()->setLocale($locale);

        $text = app(HomeContent::class)->price($this->product($price), new Skin($skin, []));

        // ICU separates with no-break spaces; compare them as plain ones.
        $this->assertSame($expected, str_replace(["\u{00A0}", "\u{202F}"], ' ', (string) $text));
    }

    public function test_the_currency_is_the_site_s_not_the_language_s(): void
    {
        config(['atrium-theme.currency' => 'USD']);

        $this->assertSame('$40', app(HomeContent::class)->price($this->product('40'), new Skin('shop', [])));
    }

    /**
     * @return array<string,array{mixed}>
     */
    public static function noPrices(): array
    {
        return ['empty' => [null], 'zero' => ['0'], 'negative' => ['-5'], 'text' => ['on request']];
    }

    #[DataProvider('noPrices')]
    public function test_a_product_without_a_real_price_shows_none(mixed $price): void
    {
        $this->assertNull(app(HomeContent::class)->price($this->product($price), new Skin('shop', [])));
    }

    /**
     * @return array<string,array{string,?string}>
     */
    public static function hours(): array
    {
        return [
            'the first paragraph, as text' => ['<p class="x">Mon&ndash;Fri <b>9–18</b></p><p>Parking in the yard</p>', 'Mon–Fri 9–18'],
            'no paragraph' => ['Mon–Fri 9–18', null],
            'an empty paragraph' => ['<p> </p><p>Parking</p>', null],
            'nothing' => ['', null],
            'over two lines' => ["<p>Mon–Fri\n9–18</p>", "Mon–Fri\n9–18"],
            'with entities' => ['<p>Fri &amp; Sat &apos;til late</p>', "Fri & Sat 'til late"],
        ];
    }

    #[DataProvider('hours')]
    public function test_the_opening_hours_are_the_first_paragraph_of_a_store(string $text, ?string $expected): void
    {
        $this->assertSame($expected, app(HomeContent::class)->hours($this->record(['detail_text' => $text])));
    }

    /**
     * @return array<string,array{?string,string}>
     */
    public static function addresses(): array
    {
        return [
            'the first line, trimmed' => ["  7 Forest Street \nLondon", '7 Forest Street'],
            'one line' => ['7 Forest Street', '7 Forest Street'],
            'empty' => ['', ''],
            'none' => [null, ''],
        ];
    }

    #[DataProvider('addresses')]
    public function test_a_store_s_address_is_its_first_line(?string $address, string $expected): void
    {
        $this->assertSame($expected, app(HomeContent::class)->address($this->record(['address' => $address])));
    }

    private function product(mixed $price): RecordView
    {
        return $this->record(['price' => $price]);
    }

    /**
     * @param  array<string,mixed>  $attributes
     */
    private function record(array $attributes): RecordView
    {
        $record = new class extends Model
        {
            protected $guarded = [];
        };

        return new RecordView($record->forceFill($attributes), app(Pages::class));
    }
}
