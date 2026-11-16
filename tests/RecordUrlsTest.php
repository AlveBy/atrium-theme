<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Models\Language;
use Atrium\Admin\Support\RecordUrls;
use Atrium\Theme\Tests\Fixtures\Gadget;
use Atrium\Theme\Tests\Fixtures\Post;
use Atrium\Theme\Tests\Fixtures\Product;
use Atrium\Theme\Tests\Fixtures\ProductSection;
use Illuminate\Database\Eloquent\Model;

/**
 * Other modules (menus, search) ask the core for the page of a record; the
 * pages under a prefix are the theme's, so the theme answers for them.
 */
final class RecordUrlsTest extends TestCase
{
    private function urls(): RecordUrls
    {
        return $this->app->make(RecordUrls::class);
    }

    private function aPost(): Post
    {
        Language::query()->create(['code' => 'de', 'name' => 'Deutsch', 'sort' => 200]);

        return Post::query()->create(['name' => ['en' => 'Hi', 'de' => 'Hallo'], 'code' => ['en' => 'hi', 'de' => 'hallo']]);
    }

    public function test_the_page_of_a_record_is_the_one_the_theme_draws(): void
    {
        $computers = ProductSection::query()->create(['name' => 'Computers', 'code' => 'computers']);
        $laptops = ProductSection::query()->create(['name' => 'Laptops', 'code' => 'laptops', 'parent_id' => $computers->id]);
        $laptop = Product::query()->create(['section_id' => $laptops->id, 'name' => 'Laptop Pro', 'code' => 'laptop-pro']);

        $this->assertSame($this->root().'/catalog/computers/laptops/laptop-pro', $this->urls()->url($laptop));
        $this->assertSame($this->root().'/catalog/computers/laptops', $this->urls()->url($laptops));
        $this->assertSame($this->root().'/catalog/computers', $this->urls()->url($computers));
    }

    public function test_a_translated_record_has_a_page_in_each_language_it_is_written_in(): void
    {
        $post = $this->aPost();

        $this->assertSame($this->root().'/posts/hi', $this->urls()->url($post, 'en'));
        $this->assertSame($this->root().'/de/posts/hallo', $this->urls()->url($post, 'de'));
        $this->assertSame($this->root().'/posts/hi', $this->urls()->url($post), 'the current language when none is given');

        $this->app->setLocale('de');

        $this->assertSame($this->root().'/de/posts/hallo', $this->urls()->url($post));
    }

    public function test_a_record_not_written_in_a_language_has_no_page_there_and_nothing_else_is_tried(): void
    {
        Language::query()->create(['code' => 'de', 'name' => 'Deutsch', 'sort' => 200]);
        // A German code but no German name: the theme draws no page, and the address rule must not make one up from the English code.
        $post = Post::query()->create(['name' => ['en' => 'Hi'], 'code' => ['en' => 'hi', 'de' => 'hallo']]);

        $this->assertSame($this->root().'/posts/hi', $this->urls()->url($post, 'en'));
        $this->assertNull($this->urls()->url($post, 'de'));
    }

    public function test_a_resolver_added_after_the_theme_is_not_asked_about_a_record_the_theme_has_no_page_for(): void
    {
        Language::query()->create(['code' => 'de', 'name' => 'Deutsch', 'sort' => 200]);
        $post = Post::query()->create(['name' => ['en' => 'Hi'], 'code' => ['en' => 'hi', 'de' => 'hallo']]);
        $this->urls()->extend(fn (): string => '/later');

        $this->assertNull($this->urls()->url($post, 'de'));
    }

    public function test_a_record_of_a_module_without_pages_is_left_to_the_next_resolver(): void
    {
        $gadget = Gadget::query()->create(['name' => 'Lamp']);

        $this->assertNull($this->urls()->url($gadget), 'nobody knows it');

        $this->urls()->extend(fn (Model $record, string $language): string => "/{$language}/gadgets/{$record->getKey()}");

        $this->assertSame("/en/gadgets/{$gadget->id}", $this->urls()->url($gadget));
        $this->assertSame("/de/gadgets/{$gadget->id}", $this->urls()->url($gadget, 'de'));
    }

    public function test_a_module_with_a_rule_but_no_prefix_is_left_to_the_next_resolver_and_the_rule(): void
    {
        config()->set('atrium.public_urls', [Post::class => ['prefix' => '']]);
        $post = $this->aPost();

        $this->assertSame(url('hi'), $this->urls()->url($post), 'the address rule: the code at the root');

        $this->urls()->extend(fn (): string => '/later');

        $this->assertSame('/later', $this->urls()->url($post));
    }

    public function test_the_theme_answers_before_the_address_rule_and_before_resolvers_added_later(): void
    {
        $post = $this->aPost();
        $this->urls()->extend(fn (): string => '/later');

        $this->assertSame($this->root().'/posts/hi', $this->urls()->url($post));
    }
}
