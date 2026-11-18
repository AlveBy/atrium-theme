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
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * A skin's home page leaves out the sections whose module is not there.
 */
final class SkinsWithoutModulesTest extends SkinsTestCase
{
    protected function modules(): array
    {
        return ['alveby/atrium-catalog', 'alveby/atrium-news'];
    }

    #[DataProvider('skins')]
    public function test_only_the_installed_modules_are_filled_and_shown(string $skin): void
    {
        $this->assertTrue($this->seedSubject($skin));

        $this->get('/')->assertOk()
            ->assertSee('data-skin="'.$skin.'"', false)
            ->assertSee('/catalog/', false)
            ->assertDontSee('class="atrium-form', false)
            ->assertDontSee('class="sk-reviews"', false)
            ->assertDontSee('sk-faq-title', false)
            ->assertDontSee('/stores/', false);
    }

    #[DataProvider('skins')]
    public function test_with_no_module_at_all_the_home_page_still_opens(string $skin): void
    {
        config(['atrium.modules' => [], 'atrium-theme.skin' => $skin]);

        $this->get('/')->assertOk()
            ->assertSee('data-skin="'.$skin.'"', false)
            ->assertDontSee('/catalog/', false)
            ->assertDontSee('/news/', false);
    }

    public function test_what_is_not_installed_gives_nothing_even_when_its_tables_are_full(): void
    {
        $this->seedSubject('shop');
        $home = new HomeContent(app(Pages::class));
        $record = HomeContent::PRODUCT::query()->firstOrFail();
        $record->forceFill(['brand_id' => 1])->save();
        $product = new RecordView($record, app(Pages::class));

        // Brands are not installed here, so their table is not there either:
        // the product's brand is not looked for there.
        $this->assertNull($home->brand($product));
        $this->assertSame([], $home->stores());
        $this->assertCount(0, $home->brands());
        $this->assertCount(0, $home->reviews(3));
        $this->assertCount(0, $home->questions(3));
        $this->assertNull($home->page('home'));
        $this->assertFalse($home->hasForm('question'));
        $this->assertNotSame([], $home->products(3));

        // The catalog and the news lose their module, keep their rows.
        config(['atrium.modules' => []]);
        $home = new HomeContent(app(Pages::class));
        $this->assertSame([], $home->products(3));
        $this->assertSame([], $home->sections());
        $this->assertSame([], $home->menu(3));
        $this->assertSame([], $home->news(3));
        $this->assertNull($home->indexUrl(HomeContent::PRODUCT));
    }
}
