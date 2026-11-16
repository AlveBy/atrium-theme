<?php

declare(strict_types=1);

namespace Atrium\Theme\Tests;

/**
 * An app with a home page of its own keeps it; the theme still draws the
 * module pages and its links lead home to the app's page.
 */
final class OwnHomePageTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['router']->get('/', fn () => 'our own home');
    }

    public function test_the_apps_own_home_page_wins(): void
    {
        $this->get('/')->assertOk()->assertSeeText('our own home');
        $this->get('/catalog')->assertOk()->assertSee('<a class="site-name" href="'.$this->root().'">', false);
    }
}
