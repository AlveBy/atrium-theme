<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Theme\RecordView;
use Atrium\Theme\Tests\Fixtures\Post;
use Atrium\Theme\Tests\Fixtures\ProductSection;

/**
 * A list shows only the records a visitor can open: one that has no page
 * (no name in the language, no code) is left out, and the rest keep their
 * order with no gap where it was.
 */
final class RecordsWithoutAddressTest extends TestCase
{
    /**
     * @param  array<int,RecordView>  $views
     * @return array<int|string,string>
     */
    private function titles(array $views): array
    {
        return array_map(fn (RecordView $view): string => $view->title(), $views);
    }

    public function test_the_home_page_leaves_out_a_record_with_no_name_in_the_language(): void
    {
        Post::query()->create(['name' => ['en' => ''], 'code' => ['en' => 'blank']]);
        Post::query()->create(['name' => ['en' => 'Alpha'], 'code' => ['en' => 'alpha']]);
        Post::query()->create(['name' => ['en' => 'Beta'], 'code' => ['en' => 'beta']]);

        $modules = $this->get('/')->assertOk()->viewData('modules');
        $posts = collect($modules)->firstWhere('label', 'Posts');

        $this->assertSame([0 => 'Alpha', 1 => 'Beta'], $this->titles($posts['records']));
    }

    public function test_the_home_page_shows_a_record_that_has_a_page(): void
    {
        Post::query()->create(['name' => ['en' => 'Alpha'], 'code' => ['en' => 'alpha']]);

        $modules = $this->get('/')->assertOk()->viewData('modules');

        $this->assertSame([0 => 'Alpha'], $this->titles(collect($modules)->firstWhere('label', 'Posts')['records']));
    }

    public function test_the_list_leaves_out_a_section_with_no_code(): void
    {
        ProductSection::query()->create(['name' => 'Nameless', 'code' => '']);
        ProductSection::query()->create(['name' => 'Computers', 'code' => 'computers']);
        ProductSection::query()->create(['name' => 'Phones', 'code' => 'phones']);

        $sections = $this->get('/catalog')->assertOk()->viewData('sections');

        $this->assertSame([0 => 'Computers', 1 => 'Phones'], $this->titles($sections));
    }

    public function test_a_section_leaves_out_a_child_with_no_code(): void
    {
        $computers = ProductSection::query()->create(['name' => 'Computers', 'code' => 'computers']);
        ProductSection::query()->create(['name' => 'Nameless', 'code' => '', 'parent_id' => $computers->id]);
        ProductSection::query()->create(['name' => 'Laptops', 'code' => 'laptops', 'parent_id' => $computers->id]);
        ProductSection::query()->create(['name' => 'Desktops', 'code' => 'desktops', 'parent_id' => $computers->id]);

        $sections = $this->get('/catalog/computers')->assertOk()->viewData('sections');

        $this->assertSame([0 => 'Laptops', 1 => 'Desktops'], $this->titles($sections));
    }
}
