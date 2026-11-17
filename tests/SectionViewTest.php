<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Theme\Tests\Fixtures\Shelf;
use Illuminate\Support\Facades\View;

/**
 * A section may be drawn with a view of its module, like a record.
 */
final class SectionViewTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('atrium.public_urls', [
            Shelf::class => ['prefix' => 'shelves', 'tree' => Shelf::class, 'parent' => 'parent_id'],
        ]);
    }

    public function test_a_section_is_drawn_with_the_view_of_its_module(): void
    {
        View::addNamespace('theme-test', __DIR__.'/Fixtures/views');
        Shelf::query()->create(['name' => 'Garden', 'code' => 'garden']);

        $this->get('/shelves/garden')
            ->assertOk()
            ->assertSee('<h1 class="shelf">Garden</h1>', false);
    }

    public function test_an_unknown_section_is_not_found(): void
    {
        View::addNamespace('theme-test', __DIR__.'/Fixtures/views');

        $this->get('/shelves/nothing')->assertNotFound();
    }
}
