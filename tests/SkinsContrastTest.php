<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * WCAG 2.2 contrast of every skin's colours, light and dark: 4.5:1 for
 * text (AA), 3:1 for the outlines of controls and the focus ring. Besides
 * the theme's pairs, each skin has pairs of its own: buttons, bands, the
 * error text of forms.
 */
final class SkinsContrastTest extends BaseTestCase
{
    private const TEXT = ['text', 'muted', 'accent', 'error'];

    private const CONTROLS = ['control', 'focus'];

    private const BACKGROUNDS = ['bg', 'surface', 'field'];

    /** Text on background pairs of each skin, 4.5:1. */
    private const PAIRS = [
        'travel' => [['on-brand', 'brand'], ['on-sun', 'sun'], ['on-band', 'band'], ['text', 'field']],
        'restaurant' => [['on-brand', 'brand'], ['text', 'field']],
        'clinic' => [['on-brand', 'brand'], ['soft-ink', 'soft'], ['text', 'soft'], ['on-footer', 'footer'], ['text', 'field']],
        'shop' => [['on-brand', 'brand'], ['deep', 'bg'], ['muted', 'bg'], ['text', 'field']],
    ];

    /** Focus rings and outlines on the skin's own bands, 3:1. */
    private const OUTLINES = [
        'travel' => [['sun', 'band'], ['on-sun', 'sun']],
        'restaurant' => [],
        'clinic' => [['on-footer', 'footer'], ['control', 'soft']],
        'shop' => [['on-brand', 'brand']],
    ];

    /**
     * @return array<string,array{0:string,1:string}>
     */
    public static function palettes(): array
    {
        $cases = [];

        foreach (array_keys(self::PAIRS) as $skin) {
            $cases["{$skin} light"] = [$skin, 'light'];
            $cases["{$skin} dark"] = [$skin, 'dark'];
        }

        return $cases;
    }

    #[DataProvider('palettes')]
    public function test_text_and_controls_are_readable(string $skin, string $palette): void
    {
        $colours = self::palette($skin, $palette);

        foreach (self::BACKGROUNDS as $background) {
            foreach (self::TEXT as $text) {
                $this->assertReadable($colours, $text, $background, 4.5, "{$skin} {$palette}");
            }

            foreach (self::CONTROLS as $control) {
                $this->assertReadable($colours, $control, $background, 3.0, "{$skin} {$palette}");
            }
        }

        foreach (self::PAIRS[$skin] as [$text, $background]) {
            $this->assertReadable($colours, $text, $background, 4.5, "{$skin} {$palette}");
        }

        foreach (self::OUTLINES[$skin] as [$outline, $background]) {
            $this->assertReadable($colours, $outline, $background, 3.0, "{$skin} {$palette}");
        }
    }

    /**
     * @return array<string,array{0:string}>
     */
    public static function skins(): array
    {
        return array_combine(array_keys(self::PAIRS), array_map(fn (string $skin) => [$skin], array_keys(self::PAIRS)));
    }

    #[DataProvider('skins')]
    public function test_the_system_dark_mode_uses_the_same_colours_as_the_switch(string $skin): void
    {
        $this->assertSame(self::palette($skin, 'dark'), self::palette($skin, 'system-dark'));
    }

    /**
     * @param  array<string,string>  $colours
     */
    private function assertReadable(array $colours, string $front, string $back, float $minimum, string $where): void
    {
        $this->assertArrayHasKey($front, $colours, "{$where}: --{$front} is set");
        $this->assertArrayHasKey($back, $colours, "{$where}: --{$back} is set");

        $ratio = self::ratio($colours[$front], $colours[$back]);
        $this->assertGreaterThanOrEqual($minimum, $ratio, "{$where}: {$front} on {$back} is {$ratio}:1");
    }

    /**
     * @return array<string,string>
     */
    private static function palette(string $skin, string $name): array
    {
        $css = (string) file_get_contents(__DIR__."/../resources/skins/{$skin}.css");
        $selector = match ($name) {
            'light' => '/(?<![\w\]"])\:root\s*\{([^}]*)\}/',
            'dark' => '/\:root\[data-theme="dark"\]\s*\{([^}]*)\}/',
            'system-dark' => '/\:root\:not\(\[data-theme="light"\]\)\s*\{([^}]*)\}/',
        };

        preg_match($selector, $css, $block);
        preg_match_all('/--([\w-]+):\s*(#[0-9a-f]{6})\s*;/i', $block[1] ?? '', $vars, PREG_SET_ORDER);

        $colours = [];

        foreach ($vars as [, $var, $hex]) {
            $colours[$var] = strtolower($hex);
        }

        return $colours;
    }

    private static function ratio(string $a, string $b): float
    {
        [$light, $dark] = [max(self::luminance($a), self::luminance($b)), min(self::luminance($a), self::luminance($b))];

        return round(($light + 0.05) / ($dark + 0.05), 2);
    }

    private static function luminance(string $hex): float
    {
        $channels = array_map(static function (string $pair): float {
            $c = hexdec($pair) / 255;

            return $c <= 0.04045 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
        }, str_split(ltrim($hex, '#'), 2));

        return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
    }
}
