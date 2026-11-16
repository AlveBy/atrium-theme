<?php

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Demo\DemoSeeder;
use Atrium\Admin\Support\PublicUrl;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\Storage;

/**
 * The demo shop of `atrium:install --demo`, as a visitor sees it: the theme
 * draws modules it has no template for, in three languages, on two sites.
 */
final class DemoSiteTest extends TestCase
{
    /**
     * MySQL and PostgreSQL keep the database between classes: the other
     * theme tests migrate it without the demo tables, so this class starts
     * with a fresh migration and leaves one for the next class.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        RefreshDatabaseState::$migrated = false;
    }

    public static function tearDownAfterClass(): void
    {
        RefreshDatabaseState::$migrated = false;
        parent::tearDownAfterClass();
    }

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        // Only the demo's own pages, not the theme tests' made-up catalog.
        $app['config']->set('atrium.public_urls', []);
        $app['config']->set('atrium.demo', true);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        (new DemoSeeder)->run();
        PublicUrl::forgetPaths();
    }

    public function test_the_home_page_shows_the_shop(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder(['Products', 'Aero 14', 'Stores', 'City centre', 'News', 'A new store in the city centre'])
            ->assertDontSee('Aero 13');
    }

    public function test_a_product_page_shows_every_kind_of_property(): void
    {
        $this->get('/catalog/computers/laptops/aero-14')
            ->assertOk()
            ->assertSee('<title>Aero 14 laptop: 1.2 kg, 18 hours of battery | Demo shop</title>', false)
            ->assertSeeInOrder(['Products', 'Computers', 'Laptops', '<h1>Aero 14</h1>'], false)
            ->assertSee('The aluminium case is 14 mm thin')
            ->assertSeeInOrder(['LM-A14', '1199.00', 'Lumen', 'Bestseller', 'Silver, Blue', 'March 12, 2026', '18 hours of battery, 1.2 kg, Matte 2.8K screen'])
            ->assertSee('<dd><a href="'.$this->root().'/catalog/audio/headphones/quiet-one">Quiet One</a>, <a href="'.$this->root().'/catalog/audio/speakers/pulse-mini">Pulse Mini</a></dd>', false)
            ->assertSee('<th>Screen</th>', false)
            ->assertSee('65 W USB-C charger<br />', false)
            ->assertSee('src="'.$this->root().'/storage/demo/laptop-open.svg"', false)
            ->assertSee('/storage/demo/quick-start.pdf" download>', false);
    }

    public function test_plain_text_and_records_outside_sections(): void
    {
        $this->get('/catalog/audio/speakers/pulse-mini')->assertOk()->assertSee('Waterproof to IP67.<br />', false);
        $this->get('/catalog/gift-card')->assertOk()->assertSee('<h1>Gift card</h1>', false);
        $this->get('/catalog/computers/laptops/aero-13')->assertNotFound();
    }

    public function test_news_are_in_the_languages_they_are_written_in(): void
    {
        $this->get('/news/a-new-store-in-the-city-centre')
            ->assertOk()
            ->assertSee('<link rel="alternate" hreflang="ru" href="'.$this->root().'/ru/news/novyj-magazin-v-centre">', false);

        $this->get('/de/news/neue-filiale-in-der-innenstadt')->assertOk()->assertSee('<html lang="de">', false);
        $this->get('/news/how-to-choose-a-laptop-for-study')->assertOk();
        $this->get('/de/news')->assertOk()->assertSee('Herbstaktion')->assertDontSee('How to choose');
    }

    public function test_the_outlet_is_a_site_of_its_own_in_german(): void
    {
        $this->get('/news/outlet-opening')->assertNotFound();

        $this->get('http://outlet.localhost/news/outlet-eroeffnung')
            ->assertOk()
            ->assertSee('<html lang="de">', false)
            ->assertSee('<h1>Das Outlet öffnet am Samstag</h1>', false)
            ->assertSee('Atrium Outlet');

        $this->get('http://outlet.localhost/news')->assertOk()->assertDontSee('Herbstaktion');
    }

    public function test_a_store_page_shows_its_address_and_phones(): void
    {
        $this->get('/stores/city-centre')
            ->assertOk()
            ->assertSee('+1 555 0100, +1 555 0101')
            ->assertSee('centre@example.com');
    }
}
