<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Skins;

use Atrium\Theme\Pages;
use Atrium\Theme\Skins\HomeContent;
use Atrium\Theme\Tests\Fixtures\Post;
use Atrium\Theme\Tests\Fixtures\Product;
use Atrium\Theme\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The home page of a skin when no module of the shop is installed: the
 * skin's own page, with the sections of the missing modules left out.
 */
final class SkinHomePageTest extends TestCase
{
    /**
     * @return array<string,array{string}>
     */
    public static function skins(): array
    {
        return ['travel' => ['travel'], 'restaurant' => ['restaurant'], 'clinic' => ['clinic'], 'shop' => ['shop'], 'atrium' => ['atrium']];
    }

    #[DataProvider('skins')]
    public function test_the_home_page_is_the_skin_s_and_opens_without_any_module(string $skin): void
    {
        config(['atrium-theme.skin' => $skin]);

        $this->get('/')->assertOk()
            ->assertViewIs('atrium-theme::skins.'.$skin.'.home')
            ->assertViewHas('home', fn (mixed $home): bool => $home instanceof HomeContent)
            ->assertSee('data-skin="'.$skin.'"', false)
            ->assertSee('<link rel="canonical" href="'.$this->root().'"', false)
            ->assertDontSee('class="atrium-form', false)
            ->assertDontSee('class="sk-reviews"', false)
            ->assertDontSee('sk-faq-title', false)
            ->assertDontSee('/stores/', false);
    }

    public function test_without_a_skin_the_home_page_is_the_neutral_one(): void
    {
        config(['atrium-theme.skin' => null]);

        $this->get('/')->assertOk()->assertViewIs('atrium-theme::home')->assertDontSee('data-skin=', false);
    }

    public function test_a_skin_renames_a_module_in_the_menu(): void
    {
        $pages = app(Pages::class);

        config(['atrium-theme.skin' => null]);
        $this->assertSame('Products', $pages->label(Product::class));

        config(['atrium-theme.skin' => 'travel']);
        $this->assertSame('Tours', $pages->label(Product::class));

        // A module the skin has no name for keeps its own.
        $this->assertSame('Posts', $pages->label(Post::class));
    }
}
