<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

/**
 * The footer says the site is made with Atrium, in the visitor's language,
 * until the site switches it off; the generator meta tag stays.
 */
final class CreditTest extends TestCase
{
    public function test_the_footer_links_to_atrium_and_the_page_names_its_generator(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<meta name="generator" content="Atrium">', false)
            ->assertSee('<a class="credit" href="https://github.com/AlveBy/atrium">Made with Atrium</a>', false);
    }

    public function test_the_credit_is_translated(): void
    {
        $this->app->setLocale('ru');

        $this->assertSame('Сделано на Atrium', __('atrium-theme::theme.credit'));
    }

    public function test_the_credit_can_be_switched_off(): void
    {
        config(['atrium-theme.credit' => false]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('class="credit"', false)
            ->assertSee('<meta name="generator" content="Atrium">', false);
    }
}
