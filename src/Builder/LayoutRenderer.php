<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Builder;

use Atrium\Admin\Builder\BlockType;
use Atrium\Admin\Builder\BlockTypes;
use Atrium\Admin\Builder\Layout;
use Atrium\Admin\Support\ContentLanguages;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\HtmlString;
use Illuminate\View\Factory;
use RuntimeException;
use Throwable;

/**
 * Draws a page's tree (Atrium\Admin\Builder\Layout) on the server: sections,
 * columns and blocks, with the styles and scripts of the block types the page
 * uses and of no others. A block of an unknown type, and one that fails, is
 * left out and told to the log; the rest of the page is drawn.
 *
 * Call it before the page's view is rendered, as the controller of the pages
 * module does: a view that fails inside makes Laravel forget the sections of
 * the views around it.
 */
final class LayoutRenderer
{
    /** Kept among the factory's "rendered once" marks to see it was reset. */
    private const MARK = 'atrium.site.layout';

    public function __construct(
        private readonly BlockTypes $types,
        private readonly ContentLanguages $languages,
        private readonly Factory $views,
        private readonly Filesystem $files,
    ) {}

    /**
     * The tree as HTML in the current language.
     *
     * @param  array<int,array<array-key,mixed>>  $sections  Layout::sections()
     */
    public function render(array $sections): HtmlString
    {
        $grid = __DIR__.'/../../resources/css/builder/layout.css';
        $assets = ['styles' => [$grid => $this->files->get($grid)], 'scripts' => []];
        $unknown = [];

        // One render for all the blocks: what a view marks @once (the script
        // of a form) is printed once however many blocks ask for it.
        $this->views->incrementRender();

        try {
            $drawn = [];

            foreach ($sections as $section) {
                $columns = [];

                foreach (Layout::nodes($section['columns'] ?? null) as $column) {
                    $blocks = [];

                    foreach (Layout::nodes($column['blocks'] ?? null) as $block) {
                        $blocks[] = $this->block($block, $assets, $unknown);
                    }

                    $columns[] = [
                        'id' => $this->id($column),
                        'span' => $this->span($column),
                        'blocks' => array_filter($blocks),
                    ];
                }

                $drawn[] = [
                    'id' => $this->id($section),
                    'width' => $this->choice($section, 'width', Layout::WIDTHS, 'default'),
                    'tone' => $this->choice($section, 'tone', Layout::TONES, 'plain'),
                    'columns' => $columns,
                ];
            }

            return new HtmlString($this->views->make('atrium-theme::builder.layout', [
                'sections' => $drawn,
                'styles' => implode("\n", $assets['styles']),
                'scripts' => $assets['scripts'],
            ])->render());
        } finally {
            $this->views->decrementRender();
            $this->views->flushStateIfDoneRendering();
        }
    }

    /**
     * @param  array<array-key,mixed>  $block
     * @param  array{styles:array<string,string>,scripts:array<string,string>}  $assets
     * @param  array<string,string>  $unknown  type => the block it was reported for
     * @return array{id:string,type:string,html:string}|null
     */
    private function block(array $block, array &$assets, array &$unknown): ?array
    {
        $id = $this->id($block);
        $key = is_string($block['type'] ?? null) ? $block['type'] : '';
        $type = $this->types->get($key);

        if ($type === null) {
            if (! isset($unknown[$key])) {
                $unknown[$key] = $id;
                report(new RuntimeException("No block type \"{$key}\": the block \"{$id}\" is left out of the page."));
            }

            return null;
        }

        $this->views->markAsRenderedOnce(self::MARK);

        try {
            $data = $type->data($this->settings($type, $block['settings'] ?? null));

            if ($data === null) {
                return null;
            }

            $html = $this->views->make($type->view(), $data)->render();
            $styles = $this->read($type->styles());
            $scripts = $this->read($type->scripts());
            $assets['styles'] += $styles;
            $assets['scripts'] += $scripts;

            return ['id' => $id, 'type' => $key, 'html' => $html];
        } catch (Throwable $error) {
            report(new RuntimeException("The block \"{$id}\" of the type \"{$key}\" could not be drawn.", previous: $error));

            return null;
        } finally {
            // A view that failed made the factory reset itself: count anew.
            if (! $this->views->hasRenderedOnce(self::MARK)) {
                $this->views->incrementRender();
            }
        }
    }

    /**
     * @param  array<int,string>  $paths
     * @return array<string,string> path => contents
     */
    private function read(array $paths): array
    {
        $contents = [];

        foreach ($paths as $path) {
            $contents[$path] = $this->files->get($path);
        }

        return $contents;
    }

    /**
     * The stored settings, with the translatable ones in the visitor's
     * language, or in the default one where it is not written.
     *
     * @return array<array-key,mixed>
     */
    private function settings(BlockType $type, mixed $settings): array
    {
        $settings = is_array($settings) ? $settings : [];

        foreach ($type->translatable() as $name) {
            $settings[$name] = $this->translated($settings[$name] ?? null);
        }

        return $settings;
    }

    private function translated(mixed $values): mixed
    {
        if (! is_array($values)) {
            return null;
        }

        foreach ([app()->getLocale(), $this->languages->default()] as $language) {
            $value = $values[$language] ?? null;

            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param  array<array-key,mixed>  $node
     */
    private function id(array $node): string
    {
        return is_string($node['id'] ?? null) ? $node['id'] : '';
    }

    /**
     * @param  array<array-key,mixed>  $node
     */
    private function setting(array $node, string $name): mixed
    {
        return is_array($node['settings'] ?? null) ? ($node['settings'][$name] ?? null) : null;
    }

    /**
     * @param  array<array-key,mixed>  $node
     * @param  array<int,string>  $allowed
     */
    private function choice(array $node, string $name, array $allowed, string $default): string
    {
        $value = $this->setting($node, $name);

        return in_array($value, $allowed, true) ? $value : $default;
    }

    /**
     * @param  array<array-key,mixed>  $column
     */
    private function span(array $column): int
    {
        $span = $this->setting($column, 'span');

        return is_numeric($span) && in_array((int) $span, range(1, Layout::MAX_COLUMNS), true) ? (int) $span : Layout::MAX_COLUMNS;
    }
}
