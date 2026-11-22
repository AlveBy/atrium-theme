<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Skins;

/**
 * The skins the theme ships with, and the one the site wears:
 * config('atrium-theme.skin'), ATRIUM_THEME_SKIN in .env. No skin, or an
 * unknown one, is the neutral theme.
 */
final class Skins
{
    public const TRAVEL = 'travel';

    public const RESTAURANT = 'restaurant';

    public const CLINIC = 'clinic';

    public const SHOP = 'shop';

    public const ATRIUM = 'atrium';

    /** @var array<string,Skin>|null */
    private ?array $skins = null;

    /**
     * @return array<string,Skin>
     */
    public function all(): array
    {
        return $this->skins ??= [
            self::TRAVEL => new Skin(self::TRAVEL, ['unbounded', 'manrope']),
            self::RESTAURANT => new Skin(self::RESTAURANT, ['cormorant-garamond', 'inter'], 'dark'),
            self::CLINIC => new Skin(self::CLINIC, ['manrope']),
            self::SHOP => new Skin(self::SHOP, ['space-grotesk', 'inter', 'jetbrains-mono']),
            self::ATRIUM => new Skin(self::ATRIUM, ['unbounded', 'inter']),
        ];
    }

    /**
     * Adds a skin of another package, or replaces one of the same name.
     */
    public function register(Skin $skin): void
    {
        $this->all();
        $this->skins[$skin->name] = $skin;
    }

    public function find(?string $name): ?Skin
    {
        return $name === null ? null : ($this->all()[$name] ?? null);
    }

    public function current(): ?Skin
    {
        $name = config('atrium-theme.skin');

        return is_string($name) ? $this->find(trim($name)) : null;
    }
}
