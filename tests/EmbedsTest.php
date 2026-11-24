<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Embeds\Embeds;
use Atrium\Admin\Facades\Hook;
use Atrium\Theme\Tests\Fixtures\NoteEmbed;
use Atrium\Theme\Tests\Fixtures\Product;
use Atrium\Theme\Tests\Fixtures\ProductSection;

/**
 * The forms and blocks an editor put into a text appear where the theme
 * shows editor HTML: the page's text, a section's text, the teasers in the
 * lists and the text blocks of the other fields.
 */
final class EmbedsTest extends TestCase
{
    private function note(int $id): string
    {
        return '<div data-atrium-embed="note" data-id="'.$id.'"></div>';
    }

    protected function setUp(): void
    {
        parent::setUp();

        Hook::register(Embeds::REGISTRY, 'note', new NoteEmbed);
    }

    public function test_the_text_of_a_page_shows_its_embed_in_the_place_of_the_placeholder(): void
    {
        $section = ProductSection::query()->create(['name' => 'Computers', 'code' => 'computers', 'description' => '<p>Intro</p>'.$this->note(7)]);
        Product::query()->create([
            'section_id' => $section->id,
            'name' => 'Laptop Pro',
            'code' => 'laptop-pro',
            'preview_text' => '<p>Teaser</p>'.$this->note(2),
            'preview_text_type' => 'html',
            'detail_text' => '<p>Before</p>'.$this->note(1).'<p>After</p>',
            'specs' => '<ul><li>16 GB</li></ul>'.$this->note(3),
        ]);

        $this->get('/catalog/computers/laptop-pro')
            ->assertOk()
            ->assertSeeInOrder(['<p>Before</p>', '<aside>NOTE 1</aside>', '<p>After</p>'], false)
            ->assertSee('<aside>NOTE 3</aside>', false)
            ->assertDontSee('data-atrium-embed', false);

        $this->get('/catalog/computers')
            ->assertOk()
            ->assertSee('<aside>NOTE 7</aside>', false)
            ->assertSee('<aside>NOTE 2</aside>', false)
            ->assertDontSee('data-atrium-embed', false);
    }

    public function test_a_page_with_a_placeholder_of_a_missing_type_still_opens_and_shows_nothing(): void
    {
        $section = ProductSection::query()->create(['name' => 'Computers', 'code' => 'computers']);
        Product::query()->create(['section_id' => $section->id, 'name' => 'Laptop Pro', 'code' => 'laptop-pro', 'detail_text' => '<p>Text</p><div data-atrium-embed="gone" data-id="1"></div>']);

        $this->get('/catalog/computers/laptop-pro')->assertOk()->assertSee('<p>Text</p>', false)->assertDontSee('data-atrium-embed', false);
    }
}
