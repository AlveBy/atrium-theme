<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Facades\Hook;

/**
 * The theme's frame has a slot at every place a module may want to add to,
 * the same with a skin and without one.
 */
final class SlotsTest extends TestCase
{
    private const SLOTS = ['body.start', 'header.start', 'header.end', 'content.before', 'content.after', 'footer.start', 'footer.end', 'body.end'];

    protected function setUp(): void
    {
        parent::setUp();

        foreach (self::SLOTS as $slot) {
            Hook::slot($slot, static fn (array $page): string => "<!-- {$slot} of {$page['siteName']} -->");
        }
    }

    public function test_every_slot_is_in_its_place_in_the_frame(): void
    {
        $this->get('/')->assertOk()->assertSeeInOrder([
            '<body>', '<!-- body.start of', 'class="skip"',
            '<header class="site-header">', '<!-- header.start of', 'class="site-name"',
            'theme-toggle', '<!-- header.end of', '</header>',
            '<main id="content"', '<!-- content.before of', '<!-- content.after of', '</main>',
            '<footer class="site-footer">', '<!-- footer.start of', 'class="credit"', '<!-- footer.end of', '</footer>',
            '<!-- body.end of', '</body>',
        ], false);
    }

    public function test_a_skin_s_own_header_and_footer_keep_the_slots(): void
    {
        config(['atrium-theme.skin' => 'shop']);

        $html = $this->get('/')->assertOk()->getContent();

        foreach (self::SLOTS as $slot) {
            $this->assertSame(1, substr_count($html, "<!-- {$slot} of"), $slot);
        }
        $this->assertMatchesRegularExpression('~<footer class="site-footer">\s*<!-- footer.start of .+?<!-- footer.end of [^>]+-->\s*</footer>~s', $html);
    }
}
