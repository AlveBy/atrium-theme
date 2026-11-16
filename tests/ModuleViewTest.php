<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Theme\Tests\Fixtures\Story;
use Illuminate\Support\Facades\View;

/**
 * A module may draw its pages with a view of its own (themeView).
 */
final class ModuleViewTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('atrium.public_urls.'.Story::class, ['prefix' => 'stories']);
    }

    public function test_the_module_view_draws_the_page_inside_the_theme(): void
    {
        View::addNamespace('theme-test', __DIR__.'/Fixtures/views');
        Story::query()->create(['name' => ['en' => 'Once upon a time'], 'code' => ['en' => 'once']]);

        $response = $this->get('/stories/once')
            ->assertOk()
            ->assertSee('<h1 class="story">Once upon a time</h1>', false)
            ->assertSee('<link rel="canonical" href="'.$this->root().'/stories/once">', false);

        $content = (string) $response->getContent();
        $this->assertLessThan(strpos($content, '</head>'), strpos($content, '<meta name="story" content="Once upon a time">'));

        // A view that does not exist leaves the theme's own.
        $this->get('/stories')->assertOk()->assertSee('<h1>Stories</h1>', false);
    }
}
