<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Every language of the theme has every line English has, no other, and
 * the same placeholders in each.
 */
final class TranslationsTest extends TestCase
{
    private const LANGUAGES = ['be', 'de', 'en', 'es', 'fr', 'it', 'ja', 'kk', 'pl', 'pt_BR', 'ru', 'tr', 'uk', 'uz', 'zh_CN'];

    /**
     * @return array<string,array{string}>
     */
    public static function files(): array
    {
        return ['skins' => ['skins'], 'theme' => ['theme'], 'errors' => ['errors']];
    }

    #[DataProvider('files')]
    public function test_every_language_has_the_english_lines_and_placeholders(string $file): void
    {
        $dir = dirname(__DIR__).'/lang';
        $english = Arr::dot(require $dir.'/en/'.$file.'.php');
        $languages = array_values(array_diff(scandir($dir) ?: [], ['.', '..']));

        $this->assertSame(self::LANGUAGES, $languages);

        foreach ($languages as $language) {
            $lines = Arr::dot(require $dir.'/'.$language.'/'.$file.'.php');

            $this->assertSame(array_keys($english), array_keys($lines), $language);

            foreach ($english as $key => $line) {
                $this->assertNotSame('', $lines[$key], "{$language}: {$key}");
                $this->assertSame(self::placeholders($line), self::placeholders($lines[$key]), "{$language}: {$key}");
            }
        }
    }

    public function test_a_missing_or_renamed_placeholder_is_noticed(): void
    {
        $this->assertSame([':price'], self::placeholders('from :price'));
        $this->assertSame([':price', ':rating'], self::placeholders(':rating — :price'));
        $this->assertNotSame(self::placeholders('from :price'), self::placeholders('from :prijs'));
        $this->assertSame([], self::placeholders('Book a table'));
    }

    /**
     * @return array<int,string>
     */
    private static function placeholders(mixed $line): array
    {
        preg_match_all('/:[a-z_]+/', (string) $line, $found);
        $found = $found[0];
        sort($found);

        return $found;
    }
}
