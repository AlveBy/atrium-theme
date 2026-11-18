<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests\Skins\Fixtures;

use Atrium\Admin\Models\Language;
use Atrium\Admin\Models\Site;
use Atrium\Admin\Modules\DemoSeeder;
use Atrium\Admin\Modules\Soft;
use Atrium\Admin\Support\ContentLanguages;
use Atrium\Admin\Support\Sites;
use Atrium\Theme\Skins\HomeContent;
use Illuminate\Database\Eloquent\Model;

/**
 * The content the skin tests show: every installed module that is still
 * empty gets a site of one subject (travel, restaurant, clinic, shop), and
 * the theme switches to that skin. Texts are in English, German and
 * Russian: the modules that translate get all three, the others the
 * default content language.
 *
 * The content is in content/{skin}.php; the pictures are the SVG files in
 * content/{skin}/.
 */
final class SkinContent extends DemoSeeder
{
    private const LANGUAGES = ['en' => 'English', 'de' => 'Deutsch', 'ru' => 'Русский'];

    public function __construct(private readonly string $skin) {}

    /** @var array<string,mixed> */
    private array $data = [];

    private string $language = 'en';

    /** @var array<string,Model> */
    private array $products = [];

    public function run(): bool
    {
        $this->data = require __DIR__.'/content/'.$this->skin.'.php';
        $this->languages();
        $this->siteName();
        $this->copyFiles(__DIR__.'/content/'.$this->skin);

        $seeded = false;

        foreach (['brands', 'sections', 'stores', 'products', 'news', 'reviews', 'faq', 'forms', 'pages'] as $part) {
            $seeded = $this->{$part}() || $seeded;
        }

        $this->useSkin();

        return $seeded;
    }

    /**
     * English, German and Russian; the one the application speaks comes
     * first when there are no languages yet, and is the default.
     */
    private function languages(): void
    {
        $locale = substr((string) config('app.locale', 'en'), 0, 2);
        $codes = array_keys(self::LANGUAGES);

        if (in_array($locale, $codes, true)) {
            $codes = [$locale, ...array_diff($codes, [$locale])];
        }

        foreach ($codes as $code) {
            Language::query()->firstOrCreate(['code' => $code], ['name' => self::LANGUAGES[$code], 'active' => true, 'sort' => 100 + Language::query()->count()]);
        }

        app(ContentLanguages::class)->flush();
        app(Sites::class)->flush();

        $default = app(ContentLanguages::class)->default();
        $this->language = array_key_exists($default, self::LANGUAGES) ? $default : 'en';
    }

    /**
     * The site takes the subject's name ("Horizon") while it still has the
     * one it was created with, the application's.
     */
    private function siteName(): void
    {
        $site = Site::query()->where('is_default', true)->first();

        if ($site === null || ! in_array((string) $site->name, ['', (string) config('app.name')], true)) {
            return;
        }

        $site->update(['name' => $this->text($this->data['site'])]);
        app(Sites::class)->flush();
    }

    private function brands(): bool
    {
        if (! $this->empty(HomeContent::BRAND)) {
            return false;
        }

        foreach ($this->data['brands'] as $sort => $brand) {
            HomeContent::BRAND::query()->create([
                'name' => $this->text($brand['name']), 'code' => $brand['code'], 'sort' => ($sort + 1) * 100, 'website' => $brand['website'] ?? null,
            ]);
        }

        return true;
    }

    private function sections(): bool
    {
        if (! $this->empty(HomeContent::SECTION)) {
            return false;
        }

        $sort = 0;

        foreach ($this->data['sections'] as $code => $section) {
            HomeContent::SECTION::query()->create([
                'name' => $this->text($section['name']), 'code' => $code, 'sort' => $sort += 100,
                'picture' => isset($section['picture']) ? $this->path($section['picture']) : null,
            ]);
        }

        return true;
    }

    private function stores(): bool
    {
        if (! $this->empty(HomeContent::STORE)) {
            return false;
        }

        $sort = 0;

        foreach ($this->data['stores'] as $code => $store) {
            HomeContent::STORE::query()->create([
                'name' => $this->text($store['name']), 'code' => $code, 'sort' => $sort += 100,
                'address' => $this->text($store['address']),
                'phones' => [$this->text($store['phone'])],
                // The first paragraph is the opening hours (HomeContent::hours()).
                'detail_text' => '<p>'.e($this->text($store['hours'])).'</p>'.(isset($store['text']) ? '<p>'.e($this->text($store['text'])).'</p>' : ''),
            ]);
        }

        return true;
    }

    private function products(): bool
    {
        if (! $this->empty(HomeContent::PRODUCT)) {
            return false;
        }

        $sections = Soft::available(HomeContent::SECTION) ? HomeContent::SECTION::query()->pluck('id', 'code') : collect();
        $brands = Soft::available(HomeContent::BRAND) ? HomeContent::BRAND::query()->pluck('id', 'code') : collect();
        $sort = 0;

        foreach ($this->data['products'] as $code => $product) {
            $this->products[$code] = HomeContent::PRODUCT::query()->create([
                'section_id' => isset($product['section']) ? $sections[$product['section']] ?? null : null,
                'brand_id' => isset($product['brand']) ? $brands[$product['brand']] ?? null : null,
                'name' => $this->text($product['name']),
                'code' => $code,
                'sort' => $sort += 100,
                'active_from' => now()->subMonth(),
                'preview_picture' => isset($product['picture']) ? $this->path($product['picture']) : null,
                'preview_text' => isset($product['text']) ? '<p>'.e($this->text($product['text'])).'</p>' : null,
                'detail_picture' => isset($product['picture']) ? $this->path($product['picture']) : null,
                'detail_text' => isset($product['detail']) ? $this->text($product['detail']) : null,
                'price' => $this->text($product['price']),
                'label' => $product['label'] ?? null,
            ]);
        }

        return true;
    }

    private function news(): bool
    {
        if (! $this->empty(HomeContent::NEWS)) {
            return false;
        }

        foreach ($this->data['news'] as [$daysAgo, $picture, $texts]) {
            $record = new (HomeContent::NEWS)([
                'site_id' => app(Sites::class)->default()->id,
                'active_from' => now()->subDays($daysAgo),
                'sort' => 500,
                'preview_picture' => $picture === null ? null : $this->path($picture),
            ]);

            foreach ($texts as $language => [$name, $code, $preview, $detail]) {
                $record->setTranslation('name', $language, $name)
                    ->setTranslation('code', $language, $code)
                    ->setTranslation('preview_text', $language, '<p>'.e($preview).'</p>')
                    ->setTranslation('detail_text', $language, $detail);
            }

            $record->save();
        }

        return true;
    }

    private function reviews(): bool
    {
        if (! $this->empty(HomeContent::REVIEW)) {
            return false;
        }

        foreach ($this->data['reviews'] as $index => $review) {
            $about = $this->products[$review['product'] ?? ''] ?? null;

            HomeContent::REVIEW::query()->create([
                'reviewable_type' => $about?->getMorphClass(),
                'reviewable_id' => $about?->getKey(),
                'author_name' => $this->text($review['author']),
                'rating' => $review['rating'],
                'text' => $this->text($review['text']),
                'status' => 'published',
                'posted_at' => now()->subDays(($index + 1) * 6),
            ]);
        }

        return true;
    }

    private function faq(): bool
    {
        if (! $this->empty(HomeContent::CATEGORY)) {
            return false;
        }

        $site = app(Sites::class)->default();
        $sort = 0;

        foreach ($this->data['faq'] as [$names, $questions]) {
            $category = new (HomeContent::CATEGORY)(['site_id' => $site->id, 'sort' => $sort += 100]);

            foreach ($names as $language => [$name, $code]) {
                $category->setTranslation('name', $language, $name)->setTranslation('code', $language, $code);
            }

            $category->save();

            foreach ($questions as $index => $texts) {
                $question = new (HomeContent::QUESTION)(['site_id' => $site->id, 'category_id' => $category->getKey(), 'sort' => ($index + 1) * 100]);

                foreach ($texts as $language => [$text, $answer]) {
                    $question->setTranslation('question', $language, $text)->setTranslation('answer', $language, '<p>'.e($answer).'</p>');
                }

                $question->save();
            }
        }

        return true;
    }

    private function forms(): bool
    {
        if (! $this->empty(HomeContent::FORM)) {
            return false;
        }

        foreach ($this->data['forms'] as $code => $data) {
            $form = new (HomeContent::FORM)(['site_id' => app(Sites::class)->default()->id, 'name' => $this->text($data['name']), 'code' => $code, 'active' => true]);
            $this->translate($form, ['button_label' => $data['button'], 'success_message' => $data['success']]);
            $form->save();

            foreach ($data['fields'] as $index => $field) {
                $record = new (HomeContent::FORM.'Field')(['form_id' => $form->getKey(), 'name' => $field['name'], 'type' => $field['type'], 'required' => $field['required'] ?? false, 'sort' => ($index + 1) * 10]);
                $this->translate($record, array_filter(['label' => $field['label'], 'options' => $field['options'] ?? null]));
                $record->save();
            }
        }

        return true;
    }

    private function pages(): bool
    {
        if (! $this->empty(HomeContent::PAGE)) {
            return false;
        }

        $this->pageTree($this->data['pages'], null);

        return true;
    }

    /**
     * @param  array<string,array<string,mixed>>  $pages
     */
    private function pageTree(array $pages, ?int $parent): void
    {
        $sort = 0;

        foreach ($pages as $code => $data) {
            $page = new (HomeContent::PAGE)(['site_id' => app(Sites::class)->default()->id, 'parent_id' => $parent, 'code' => $code, 'sort' => $sort += 100, 'active' => true]);
            $this->translate($page, ['name' => $data['name'], 'content' => $data['content']]);
            $page->save();

            $this->pageTree($data['children'] ?? [], (int) $page->getKey());
        }
    }

    private function useSkin(): void
    {
        config(['atrium-theme.skin' => $this->skin]);
    }

    /**
     * Whether a module is installed and has nothing yet.
     */
    private function empty(string $class): bool
    {
        return Soft::available($class) && ! $class::query()->withoutGlobalScopes()->exists();
    }

    /**
     * A text in the default content language: the value itself, or the
     * language's one from an array of en/de/ru (English when missing).
     */
    private function text(mixed $value): mixed
    {
        return is_array($value) ? ($value[$this->language] ?? $value['en']) : $value;
    }

    /**
     * Set translatable attributes in every language given.
     *
     * @param  array<string,array<string,string>|string>  $attributes
     */
    private function translate(Model $model, array $attributes): void
    {
        foreach ($attributes as $attribute => $texts) {
            // The same text in every language when it is one string ("12:00").
            // An array keyed by language sets every translation at once.
            $model->setAttribute($attribute, is_array($texts) ? $texts : array_fill_keys(array_keys(self::LANGUAGES), $texts));
        }
    }
}
