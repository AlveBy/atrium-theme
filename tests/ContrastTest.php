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
 * WCAG 2.2 contrast of the theme's colours, in both palettes: 4.5:1 for
 * text (AA), 3:1 for the outlines of controls and the focus ring.
 */
final class ContrastTest extends BaseTestCase
{
    private const TEXT = ['text', 'muted', 'accent', 'error'];

    private const CONTROLS = ['control', 'focus'];

    private const BACKGROUNDS = ['bg', 'surface'];

    /**
     * @return array<string,array{0:string}>
     */
    public static function palettes(): array
    {
        return ['light' => ['light'], 'dark' => ['dark']];
    }

    #[DataProvider('palettes')]
    public function test_text_and_controls_are_readable(string $palette): void
    {
        $colours = self::palette($palette);
        $this->assertCount(9, $colours, 'every colour of the palette is read');

        foreach (self::BACKGROUNDS as $background) {
            foreach (self::TEXT as $text) {
                $ratio = self::ratio($colours[$text], $colours[$background]);
                $this->assertGreaterThanOrEqual(4.5, $ratio, "{$palette}: {$text} on {$background} is {$ratio}:1");
            }

            foreach (self::CONTROLS as $control) {
                $ratio = self::ratio($colours[$control], $colours[$background]);
                $this->assertGreaterThanOrEqual(3.0, $ratio, "{$palette}: {$control} on {$background} is {$ratio}:1");
            }
        }
    }

    public function test_the_system_dark_mode_uses_the_same_colours_as_the_switch(): void
    {
        $this->assertSame(self::palette('dark'), self::palette('system-dark'));
    }

    /**
     * @return array<string,string>
     */
    private static function palette(string $name): array
    {
        $css = (string) file_get_contents(__DIR__.'/../resources/css/theme.css');
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
