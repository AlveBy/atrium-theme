<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Builder\BlockType;
use Atrium\Admin\Builder\BlockTypes;
use Atrium\Admin\Facades\Hook;
use Atrium\Admin\Models\Language;
use Atrium\Admin\Support\ContentLanguages;
use Atrium\Theme\Builder\LayoutRenderer;
use Atrium\Theme\Tests\Fixtures\ProbeBlock;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\View;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;

/**
 * A page's tree becomes HTML: the grid, the blocks, the files of the block
 * types the page uses, and nothing broken by a block that cannot be drawn.
 */
final class LayoutRendererTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        View::addNamespace('probe', __DIR__.'/Fixtures/builder');
    }

    private function type(string $key, BlockType $type): void
    {
        Hook::register(BlockTypes::REGISTRY, $key, $type);
    }

    /**
     * @param  array<array-key,mixed>  $settings
     * @return array<string,mixed>
     */
    private function block(string $id, string $type, array $settings = []): array
    {
        return ['id' => $id, 'type' => $type, 'settings' => $settings];
    }

    /**
     * One section with one column.
     *
     * @return array<int,array<string,mixed>>
     */
    private function page(mixed ...$blocks): array
    {
        return [['id' => 's1', 'settings' => [], 'columns' => [['id' => 'c1', 'settings' => [], 'blocks' => $blocks]]]];
    }

    /**
     * @param  array<int,array<array-key,mixed>>  $sections
     */
    private function render(array $sections): string
    {
        return $this->app->make(LayoutRenderer::class)->render($sections)->toHtml();
    }

    private function file(string $path): string
    {
        return (string) file_get_contents($path);
    }

    private function gridCss(): string
    {
        return $this->file(__DIR__.'/../resources/css/builder/layout.css');
    }

    public function test_sections_columns_and_blocks_are_drawn_in_their_order(): void
    {
        $this->type('probe', new ProbeBlock);
        $first = ['id' => 's1', 'settings' => ['width' => 'narrow', 'tone' => 'tinted'], 'columns' => [
            ['id' => 'c1', 'settings' => ['span' => 4], 'blocks' => [$this->block('a', 'probe', ['text' => 'Alpha']), $this->block('b', 'probe', ['text' => 'Beta'])]],
            ['id' => 'c2', 'settings' => ['span' => 8], 'blocks' => [$this->block('c', 'probe', ['text' => 'Gamma'])]],
        ]];
        $second = ['id' => 's2', 'settings' => ['width' => 'full', 'tone' => 'plain'], 'columns' => [['id' => 'c3', 'settings' => [], 'blocks' => []]]];

        $html = $this->render([$first, $second]);

        $at = 0;

        foreach ([
            '<div class="atrium-layout">',
            '<section class="b-section b-width-narrow b-tone-tinted" data-section-id="s1">',
            '<div class="b-inner">',
            '<div class="b-column b-span-4" data-column-id="c1">',
            '<div class="b-block b-type-probe" data-block-id="a"><p class="probe">Alpha</p>',
            '<div class="b-block b-type-probe" data-block-id="b"><p class="probe">Beta</p>',
            '<div class="b-column b-span-8" data-column-id="c2">',
            '<div class="b-block b-type-probe" data-block-id="c"><p class="probe">Gamma</p>',
            '<section class="b-section b-width-full b-tone-plain" data-section-id="s2">',
            '<div class="b-column b-span-12" data-column-id="c3">',
        ] as $part) {
            $found = strpos($html, $part, $at);
            $this->assertNotFalse($found, $part);
            $at = $found + strlen($part);
        }
    }

    public function test_a_page_without_sections_has_only_the_grid_styles(): void
    {
        $html = $this->render([]);

        $this->assertStringContainsString('<style>'.$this->gridCss().'</style>', $html);
        $this->assertStringNotContainsString('<section', $html);
        $this->assertStringNotContainsString('<script', $html);
    }

    /**
     * @return array<string,array{mixed,string}>
     */
    public static function sectionSettings(): array
    {
        return [
            'nothing stored' => [[], 'b-width-default b-tone-plain'],
            'not a map' => ['narrow', 'b-width-default b-tone-plain'],
            'unknown words' => [['width' => 'huge', 'tone' => 'neon'], 'b-width-default b-tone-plain'],
            'true is not a word' => [['width' => true, 'tone' => true], 'b-width-default b-tone-plain'],
            'default and tinted' => [['width' => 'default', 'tone' => 'tinted'], 'b-width-default b-tone-tinted'],
            'narrow only' => [['width' => 'narrow'], 'b-width-narrow b-tone-plain'],
        ];
    }

    #[DataProvider('sectionSettings')]
    public function test_a_section_takes_only_the_widths_and_tones_there_are(mixed $settings, string $classes): void
    {
        $html = $this->render([['id' => 's1', 'settings' => $settings, 'columns' => []]]);

        $this->assertStringContainsString('<section class="b-section '.$classes.'" data-section-id="s1">', $html);
    }

    /**
     * @return array<string,array{mixed,int}>
     */
    public static function spans(): array
    {
        return [
            'the narrowest' => [['span' => 1], 1],
            'the widest' => [['span' => 12], 12],
            'one short of the widest' => [['span' => 11], 11],
            'written as text' => [['span' => '6'], 6],
            'a fraction loses its tail' => [['span' => 6.7], 6],
            'zero' => [['span' => 0], 12],
            'too wide' => [['span' => 13], 12],
            'negative' => [['span' => -1], 12],
            'less than one' => [['span' => '0.5'], 12],
            'a word' => [['span' => 'half'], 12],
            'a list' => [['span' => ['6']], 12],
            'nothing stored' => [[], 12],
            'not a map' => ['6', 12],
        ];
    }

    #[DataProvider('spans')]
    public function test_a_column_spans_between_one_and_twelve_parts(mixed $settings, int $span): void
    {
        $html = $this->render([['id' => 's1', 'columns' => [['id' => 'c1', 'settings' => $settings, 'blocks' => []]]]]);

        $this->assertStringContainsString('<div class="b-column b-span-'.$span.'" data-column-id="c1">', $html);
    }

    public function test_identifiers_that_are_not_text_are_left_empty(): void
    {
        $this->type('probe', new ProbeBlock);

        $html = $this->render([['id' => 5, 'columns' => [['blocks' => [['id' => ['x'], 'type' => 'probe', 'settings' => []]]]]]]);

        $this->assertStringContainsString('data-section-id=""', $html);
        $this->assertStringContainsString('data-column-id=""', $html);
        $this->assertStringContainsString('data-block-id=""', $html);
    }

    public function test_what_is_not_a_column_or_a_block_is_skipped(): void
    {
        $this->type('probe', new ProbeBlock);

        $html = $this->render([
            ['id' => 's1', 'columns' => 'none'],
            ['id' => 's2', 'columns' => ['junk', ['id' => 'c1', 'blocks' => ['junk', $this->block('a', 'probe', ['text' => 'Alpha'])]], ['id' => 'c2', 'blocks' => 7]]],
        ]);

        $this->assertSame(2, substr_count($html, '<section'));
        $this->assertSame(2, substr_count($html, 'data-column-id='));
        $this->assertSame(1, substr_count($html, 'data-block-id='));
    }

    public function test_a_page_gets_the_files_of_the_types_it_uses_once_and_of_no_others(): void
    {
        $this->type('probe', new ProbeBlock(styles: ['probe.css'], scripts: ['probe.js']));
        $this->type('other', new ProbeBlock(styles: ['second.css']));

        $html = $this->render($this->page($this->block('a', 'probe'), $this->block('b', 'probe')));

        $css = $this->file(ProbeBlock::path('probe.css'));
        $this->assertStringContainsString('<style>'.$this->gridCss()."\n".$css.'</style>', $html);
        $this->assertSame(1, substr_count($html, $css));
        $this->assertSame(1, substr_count($html, '<script>'.$this->file(ProbeBlock::path('probe.js')).'</script>'));
        $this->assertSame(1, substr_count($html, '<script'));
        $this->assertStringNotContainsString('.second', $html);
    }

    public function test_every_type_on_a_page_adds_its_own_files(): void
    {
        $this->type('probe', new ProbeBlock(styles: ['probe.css'], scripts: ['probe.js']));
        $this->type('other', new ProbeBlock(styles: ['second.css'], scripts: ['second.js']));

        $html = $this->render($this->page($this->block('a', 'probe'), $this->block('b', 'other')));

        $this->assertStringContainsString($this->file(ProbeBlock::path('probe.css'))."\n".$this->file(ProbeBlock::path('second.css')).'</style>', $html);
        $this->assertStringContainsString('<script>'.$this->file(ProbeBlock::path('probe.js')).'</script>', $html);
        $this->assertStringContainsString('<script>'.$this->file(ProbeBlock::path('second.js')).'</script>', $html);
        $this->assertSame(2, substr_count($html, '<script'));
    }

    public function test_a_type_may_have_several_files_of_a_kind(): void
    {
        $this->type('probe', new ProbeBlock(styles: ['probe.css', 'second.css'], scripts: ['probe.js', 'second.js']));

        $html = $this->render($this->page($this->block('a', 'probe')));

        $this->assertStringContainsString($this->file(ProbeBlock::path('probe.css'))."\n".$this->file(ProbeBlock::path('second.css')).'</style>', $html);

        foreach (['probe.js', 'second.js'] as $script) {
            $this->assertStringContainsString('<script>'.$this->file(ProbeBlock::path($script)).'</script>', $html);
        }
    }

    public function test_a_block_with_nothing_to_draw_leaves_neither_a_box_nor_files(): void
    {
        Exceptions::fake();
        $this->type('empty', new ProbeBlock(styles: ['probe.css'], scripts: ['probe.js'], data: static fn (): ?array => null));

        $html = $this->render($this->page($this->block('a', 'empty')));

        $this->assertStringNotContainsString('data-block-id', $html);
        $this->assertStringNotContainsString('.probe', $html);
        $this->assertStringNotContainsString('<script', $html);
        Exceptions::assertNothingReported();
    }

    public function test_a_block_of_an_unknown_type_is_left_out_and_each_such_type_is_reported_once(): void
    {
        Exceptions::fake();
        $this->type('probe', new ProbeBlock);

        $html = $this->render($this->page(
            $this->block('a', 'gone'),
            $this->block('b', 'probe', ['text' => 'Still here']),
            $this->block('c', 'gone'),
            $this->block('d', 'lost'),
            ['id' => 'e', 'type' => ['probe']],
        ));

        $this->assertStringContainsString('<p class="probe">Still here</p>', $html);
        $this->assertSame(1, substr_count($html, 'data-block-id='));
        Exceptions::assertReportedCount(3);

        foreach ([
            'No block type "gone": the block "a" is left out of the page.',
            'No block type "lost": the block "d" is left out of the page.',
            'No block type "": the block "e" is left out of the page.',
        ] as $message) {
            Exceptions::assertReported(static fn (RuntimeException $error): bool => $error->getMessage() === $message);
        }
    }

    public function test_a_type_that_is_not_available_is_treated_as_unknown(): void
    {
        Exceptions::fake();
        Hook::register(BlockTypes::REGISTRY, 'off', new class extends BlockType
        {
            public function label(): string
            {
                return 'Off';
            }

            public function view(): string
            {
                return 'probe::text';
            }

            public function available(): bool
            {
                return false;
            }
        });

        $html = $this->render($this->page($this->block('a', 'off')));

        $this->assertStringNotContainsString('data-block-id', $html);
        Exceptions::assertReported(static fn (RuntimeException $error): bool => $error->getMessage() === 'No block type "off": the block "a" is left out of the page.');
    }

    public function test_a_block_whose_data_fails_is_left_out_and_reported_with_the_reason(): void
    {
        Exceptions::fake();
        $reason = new RuntimeException('no data today');
        $this->type('probe', new ProbeBlock);
        $this->type('bad', new ProbeBlock(styles: ['second.css'], data: static fn () => throw $reason));

        $html = $this->render($this->page($this->block('a', 'bad'), $this->block('b', 'probe', ['text' => 'Still here'])));

        $this->assertStringContainsString('<p class="probe">Still here</p>', $html);
        $this->assertSame(1, substr_count($html, 'data-block-id='));
        $this->assertStringNotContainsString('.second', $html);
        Exceptions::assertReportedCount(1);
        Exceptions::assertReported(static fn (RuntimeException $error): bool => $error->getMessage() === 'The block "a" of the type "bad" could not be drawn.'
            && $error->getPrevious() === $reason);
    }

    public function test_a_block_whose_view_fails_leaves_no_half_of_itself(): void
    {
        Exceptions::fake();
        $this->type('probe', new ProbeBlock);
        $this->type('bad', new ProbeBlock('probe::broken', styles: ['second.css']));

        $html = $this->render($this->page($this->block('a', 'bad'), $this->block('b', 'probe', ['text' => 'Still here'])));

        $this->assertStringContainsString('<p class="probe">Still here</p>', $html);
        $this->assertStringNotContainsString('half', $html);
        $this->assertStringNotContainsString('.second', $html);
        Exceptions::assertReported(static fn (RuntimeException $error): bool => $error->getMessage() === 'The block "a" of the type "bad" could not be drawn.'
            && str_contains((string) $error->getPrevious()?->getMessage(), 'the view broke'));
    }

    public function test_a_block_with_a_missing_file_adds_none_of_its_files(): void
    {
        Exceptions::fake();
        $this->type('bad', new ProbeBlock(styles: ['second.css'], scripts: ['missing.js']));

        $html = $this->render($this->page($this->block('a', 'bad')));

        $this->assertStringNotContainsString('data-block-id', $html);
        $this->assertStringNotContainsString('.second', $html);
        Exceptions::assertReported(static fn (RuntimeException $error): bool => $error->getPrevious() instanceof FileNotFoundException);
    }

    public function test_what_a_view_prints_once_is_printed_once_however_many_blocks_ask(): void
    {
        $this->type('once', new ProbeBlock('probe::once'));
        $page = $this->page($this->block('a', 'once', ['text' => 'A']), $this->block('b', 'once', ['text' => 'B']));

        $first = $this->render($page);
        // A new page starts over.
        $second = $this->render($page);

        $this->assertSame(1, substr_count($first, '<i>ONCE</i>'));
        $this->assertSame(1, substr_count($second, '<i>ONCE</i>'));
        $this->assertStringContainsString('<b>B</b>', $first);
    }

    public function test_a_block_with_failing_data_does_not_make_the_next_one_print_again(): void
    {
        Exceptions::fake();
        $this->type('once', new ProbeBlock('probe::once'));
        $this->type('bad', new ProbeBlock(data: static fn () => throw new RuntimeException('no')));

        $html = $this->render($this->page($this->block('a', 'once'), $this->block('b', 'bad'), $this->block('c', 'once')));

        $this->assertSame(1, substr_count($html, '<i>ONCE</i>'));
    }

    public function test_after_a_failing_view_the_later_blocks_still_share_what_is_printed_once(): void
    {
        Exceptions::fake();
        $this->type('once', new ProbeBlock('probe::once'));
        $this->type('bad', new ProbeBlock('probe::broken'));
        $page = $this->page($this->block('a', 'once'), $this->block('b', 'bad'), $this->block('c', 'once'), $this->block('d', 'once'));

        $html = $this->render($page);

        // Once before the failure, which made Laravel forget, and once after.
        $this->assertSame(2, substr_count($html, '<i>ONCE</i>'));
        $this->assertSame(2, substr_count($this->render($page), '<i>ONCE</i>'));
    }

    public function test_a_type_gets_its_translatable_settings_in_the_visitors_language_or_the_default_one(): void
    {
        Language::query()->create(['code' => 'de', 'name' => 'Deutsch', 'sort' => 200]);
        $this->app->make(ContentLanguages::class)->forgetActive();
        $probe = new ProbeBlock(translatable: ['text', 'note']);
        $this->type('probe', $probe);
        $page = $this->page(
            $this->block('a', 'probe', ['text' => ['en' => 'Hello', 'de' => 'Hallo'], 'note' => ['en' => 'Only English'], 'plain' => ['en' => 'kept']]),
            $this->block('b', 'probe', ['text' => ['en' => 'Fallback', 'de' => ''], 'note' => ['de' => '0']]),
            $this->block('c', 'probe', ['text' => ['fr' => 'Salut'], 'note' => ['en' => null, 'de' => null]]),
            $this->block('d', 'probe', ['text' => 'not by language']),
            $this->block('e', 'probe', ['text' => new \ArrayObject(['en' => 'no list'])]),
        );

        $this->app->setLocale('de');
        $this->render($page);

        $this->assertSame([
            ['text' => 'Hallo', 'note' => 'Only English', 'plain' => ['en' => 'kept']],
            ['text' => 'Fallback', 'note' => '0'],
            ['text' => null, 'note' => null],
            ['text' => null, 'note' => null],
            ['text' => null, 'note' => null],
        ], $probe->given);
    }

    public function test_the_visitors_language_goes_before_the_default_one(): void
    {
        Language::query()->create(['code' => 'de', 'name' => 'Deutsch', 'sort' => 200]);
        $this->app->make(ContentLanguages::class)->forgetActive();
        $this->type('probe', new ProbeBlock(translatable: ['text']));
        $page = $this->page($this->block('a', 'probe', ['text' => ['en' => 'Hello', 'de' => 'Hallo']]));

        $this->assertStringContainsString('<p class="probe">Hello</p>', $this->render($page));

        $this->app->setLocale('de');

        $this->assertStringContainsString('<p class="probe">Hallo</p>', $this->render($page));
    }

    public function test_settings_that_are_not_a_map_are_taken_as_none(): void
    {
        $plain = new ProbeBlock;
        $translated = new ProbeBlock(translatable: ['text']);
        $this->type('plain', $plain);
        $this->type('translated', $translated);

        $html = $this->render($this->page(['id' => 'a', 'type' => 'plain', 'settings' => 'x'], ['id' => 'b', 'type' => 'translated', 'settings' => 'x'], ['id' => 'c', 'type' => 'plain']));

        $this->assertSame(3, substr_count($html, 'data-block-id='));
        $this->assertSame([[], []], $plain->given);
        $this->assertSame([['text' => null]], $translated->given);
    }
}
