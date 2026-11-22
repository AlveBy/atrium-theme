<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Skins;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\HtmlString;

/**
 * One look of the theme for a kind of site: its colours and fonts (a
 * stylesheet of custom properties), its home page and the partials the
 * home page is made of. Inner pages keep the theme's templates in the
 * skin's colours and fonts.
 */
final class Skin
{
    /** Font files: family => [CSS name, [subset => unicode-range]] */
    private const FONTS = [
        'unbounded' => ['Unbounded', ['latin', 'latin-ext', 'cyrillic', 'cyrillic-ext']],
        'manrope' => ['Manrope', ['latin', 'latin-ext', 'cyrillic', 'cyrillic-ext']],
        'cormorant-garamond' => ['Cormorant Garamond', ['latin', 'latin-ext', 'cyrillic', 'cyrillic-ext']],
        'inter' => ['Inter', ['latin', 'latin-ext', 'cyrillic', 'cyrillic-ext']],
        'space-grotesk' => ['Space Grotesk', ['latin', 'latin-ext']],
        'jetbrains-mono' => ['JetBrains Mono', ['latin', 'latin-ext', 'cyrillic', 'cyrillic-ext']],
    ];

    /** The subsets of Fontsource: Polish, Turkish and Uzbek need latin-ext, Kazakh cyrillic-ext. */
    private const RANGES = [
        'latin' => 'U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD',
        'latin-ext' => 'U+0100-02BA,U+02BD-02C5,U+02C7-02CC,U+02CE-02D7,U+02DD-02FF,U+0304,U+0308,U+0329,U+1D00-1DBF,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20C0,U+2113,U+2C60-2C7F,U+A720-A7FF',
        'cyrillic' => 'U+0301,U+0400-045F,U+0490-0491,U+04B0-04B1,U+2116',
        'cyrillic-ext' => 'U+0460-052F,U+1C80-1C8A,U+20B4,U+2DE0-2DFF,U+A640-A69F,U+FE2E-FE2F',
    ];

    /** Where `vendor:publish --tag=atrium-theme-assets` puts the fonts. */
    public const FONTS_PATH = 'vendor/atrium-theme/fonts';

    /**
     * A skin of another package names its own resources folder (with
     * `skins/{name}.css` and `skins/{name}/*.svg`) and view namespace (views
     * `skins/{name}/…`, texts `skins.{name}.…`); the theme's skins use the
     * theme's.
     *
     * @param  array<int,string>  $fonts  font families, keys of FONTS
     * @param  'light'|'dark'  $scheme  the colours the skin opens with
     */
    public function __construct(
        public readonly string $name,
        public readonly array $fonts,
        public readonly string $scheme = 'light',
        public readonly ?string $path = null,
        public readonly string $views = 'atrium-theme',
    ) {}

    /**
     * The skin's stylesheet: the @font-face rules of its fonts (for the
     * fonts that are published; without them the stacks fall back to the
     * system fonts), what every skin shares, then the skin's own rules.
     */
    public function styles(): string
    {
        return $this->fontFaces()
            .File::get(self::resource('skins/base.css'))
            .File::get($this->own("skins/{$this->name}.css"));
    }

    /**
     * A view of the skin: "home", "header", …
     */
    public function view(string $name): string
    {
        return "{$this->views}::skins.{$this->name}.{$name}";
    }

    /**
     * Whether the skin draws a view of its own: "list", "page", …
     */
    public function hasView(string $name): bool
    {
        return view()->exists($this->view($name));
    }

    /**
     * A text of the skin in the page's language.
     *
     * @param  array<string,string|int>  $replace
     */
    public function text(string $key, array $replace = []): string
    {
        return (string) __("{$this->views}::skins.{$this->name}.{$key}", $replace);
    }

    /**
     * Whether the skin has a text, in the page's language or the fallback.
     */
    public function hasText(string $key): bool
    {
        return Lang::has("{$this->views}::skins.{$this->name}.{$key}");
    }

    /**
     * An illustration of the skin, drawn inline: decoration, hidden from
     * screen readers.
     */
    public function illustration(string $file): HtmlString
    {
        $svg = File::get($this->own("skins/{$this->name}/{$file}.svg"));

        return new HtmlString(preg_replace('/^<svg /', '<svg aria-hidden="true" focusable="false" ', trim($svg)) ?? '');
    }

    /**
     * The font files every skin can use, for vendor:publish.
     */
    public static function fontsDirectory(): string
    {
        return self::resource('fonts');
    }

    private function fontFaces(): string
    {
        $css = '';

        foreach (array_intersect($this->fonts, array_keys(self::FONTS)) as $font) {
            [$family, $subsets] = self::FONTS[$font];

            foreach (['normal', 'italic'] as $style) {
                foreach ($subsets as $subset) {
                    $file = "{$font}-{$subset}-wght-{$style}.woff2";

                    if (! is_file(public_path(self::FONTS_PATH.'/'.$file))) {
                        continue;
                    }

                    $css .= sprintf(
                        "@font-face{font-family:'%s';font-style:%s;font-display:swap;font-weight:100 900;src:url(%s) format('woff2');unicode-range:%s}\n",
                        $family, $style, asset(self::FONTS_PATH.'/'.$file), self::RANGES[$subset],
                    );
                }
            }
        }

        return $css;
    }

    private function own(string $path): string
    {
        return $this->path === null ? self::resource($path) : $this->path.'/'.$path;
    }

    private static function resource(string $path): string
    {
        return dirname(__DIR__, 2).'/resources/'.$path;
    }
}
