<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Skins;

use Atrium\Theme\Skins\Skin;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Each skin's home page, every module installed and filled with the demo
 * of its subject.
 */
final class SkinsTest extends SkinsTestCase
{
    /**
     * What the home page of each subject shows: its own sections, as in the
     * design, each filled from a module.
     */
    private const SEES = [
        // pages, catalog, reviews, FAQ, forms, stores (the phone in the header)
        'travel' => ['Trips put together around you', 'Altai', 'class="sk-reviews"', 'Olga', 'sk-faq-title', 'Find me a tour', '+44 20 0000 0000'],
        // pages, catalog by section, one review, stores, forms
        'restaurant' => ['Home cooking you will want to <em>repeat</em>', 'Cottage cheese pancakes with berries', 'Marina', 'On Garden Street', 'class="atrium-form'],
        // pages (doctors), catalog, news, FAQ, forms, stores
        'clinic' => ['A medical centre close to home', 'General practice', 'Elena', 'A room for 24-hour blood pressure monitoring has opened', 'sk-faq-title', 'class="atrium-form', '+44 20 0000 0000'],
        // pages, news (the banner), catalog, brands, stores
        'shop' => ['Things for the home that last for years', 'Next-day delivery in town', 'Drop vase', 'Clay &amp; Co', 'On Forest Street'],
    ];

    /**
     * The home page's heading of each subject, in Russian and German.
     */
    private const HEADINGS = [
        'travel' => ['Путешествия, которые собирают под вас', 'Reisen, die wir um Sie herum planen'],
        'restaurant' => ['Кухня, которую хочется <em>повторить</em>', 'Küche zum <em>Wiederkommen</em>'],
        'clinic' => ['Медицинский центр рядом с домом', 'Ein medizinisches Zentrum in Ihrer Nähe'],
        'shop' => ['Вещи для дома, которые служат годами', 'Dinge für Zuhause, die Jahre halten'],
    ];

    protected function modules(): array
    {
        return self::ALL_MODULES;
    }

    #[DataProvider('skins')]
    public function test_the_demo_fills_every_module_and_the_home_page_shows_them(string $skin): void
    {
        $this->assertTrue($this->seedSubject($skin));
        $this->assertSame($skin, config('atrium-theme.skin'));
        Storage::disk('public')->assertExists('demo');

        $response = $this->get('/')->assertOk()
            ->assertSee('data-skin="'.$skin.'"', false)
            ->assertSee(':root[data-skin="'.$skin.'"]', false)
            ->assertDontSee('fonts.googleapis.com', false)
            ->assertDontSee('fonts.gstatic.com', false)
            ->assertSee('<meta name="generator" content="Atrium">', false)
            ->assertSee('<link rel="canonical" href="'.url('/').'">', false)
            ->assertSee('<a class="credit" href="https://github.com/AlveBy/atrium">Made with Atrium</a>', false);

        foreach (self::SEES[$skin] as $text) {
            $response->assertSee($text, false);
        }

        // A second run finds the content there and adds nothing.
        $this->assertFalse($this->seedSubject($skin));

        // A site can switch the credit off in a skin too.
        config(['atrium-theme.credit' => false]);
        $this->get('/')->assertOk()->assertDontSee('class="credit"', false);
    }

    #[DataProvider('skins')]
    public function test_the_home_page_speaks_the_other_languages(string $skin): void
    {
        $this->seedSubject($skin);
        [$ru, $de] = self::HEADINGS[$skin];

        $this->get('/ru')->assertOk()->assertSee('lang="ru"', false)->assertSee($ru, false);
        $this->get('/de')->assertOk()->assertSee('lang="de"', false)->assertSee($de, false);
    }

    #[DataProvider('skins')]
    public function test_the_fonts_come_from_the_site_once_they_are_published(string $skin): void
    {
        $this->seedSubject($skin);
        $this->get('/')->assertOk()->assertDontSee('@font-face', false);

        $public = sys_get_temp_dir().'/atrium-skin-'.uniqid();
        (new Filesystem)->copyDirectory(Skin::fontsDirectory(), $public.'/'.Skin::FONTS_PATH);
        $this->app->usePublicPath($public);

        try {
            $this->get('/')->assertOk()
                ->assertSee('@font-face', false)
                ->assertSee(asset(Skin::FONTS_PATH.'/'), false)
                ->assertDontSee('fonts.googleapis.com', false);
        } finally {
            (new Filesystem)->deleteDirectory($public);
        }
    }

    public function test_without_a_skin_the_home_page_is_the_neutral_theme(): void
    {
        $this->seedSubject('travel');
        config(['atrium-theme.skin' => null]);

        $this->get('/')->assertOk()->assertDontSee('data-skin=', false)->assertDontSee('@font-face', false);
    }
}
