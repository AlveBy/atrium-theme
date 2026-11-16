<?php

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Models\Language;
use Atrium\Theme\Tests\Fixtures\Brand;
use Atrium\Theme\Tests\Fixtures\Post;
use Atrium\Theme\Tests\Fixtures\Product;
use Atrium\Theme\Tests\Fixtures\ProductSection;

/**
 * The theme draws modules it has never seen: no template per module, only
 * the admin form's fields.
 */
final class PagesTest extends TestCase
{
    private ProductSection $computers;

    private ProductSection $laptops;

    private Product $laptop;

    protected function setUp(): void
    {
        parent::setUp();

        $this->computers = ProductSection::query()->create(['name' => 'Computers', 'code' => 'computers', 'description' => '<p>Everything that computes.</p>']);
        $this->laptops = ProductSection::query()->create(['name' => 'Laptops', 'code' => 'laptops', 'parent_id' => $this->computers->id]);
        ProductSection::query()->create(['name' => 'Hidden', 'code' => 'hidden', 'active' => false]);

        $brand = Brand::query()->create(['name' => 'Northwind', 'xml_id' => 'northwind']);

        $this->laptop = Product::query()->create([
            'section_id' => $this->laptops->id,
            'name' => 'Laptop Pro',
            'code' => 'laptop-pro',
            'sort' => 100,
            'active_from' => '2026-03-01 10:00:00',
            'preview_picture' => '/upload/iblock/1/laptop.jpg',
            'preview_text' => "Light & fast.\nAll day.",
            'preview_text_type' => 'text',
            'detail_text' => '<p>The <strong>best</strong> laptop.</p>',
            'article' => 'LP-001',
            'price' => 1999.5,
            'color' => 'silver',
            'brand' => $brand->id,
            'orderable' => 'Y',
            'specs' => '<ul><li>16 GB</li></ul>',
            'photos' => json_encode(['/upload/iblock/1/a.jpg', 'products/b.jpg']),
            'manual' => '/upload/iblock/1/manual%20v2.pdf',
        ]);

        Product::query()->create(['section_id' => $this->laptops->id, 'name' => 'Laptop Air', 'code' => 'laptop-air', 'sort' => 200]);
        Product::query()->create(['name' => 'Gift card', 'code' => 'gift-card', 'sort' => 300]);
        Product::query()->create(['name' => 'Retired', 'code' => 'retired', 'active' => false]);
        Product::query()->create(['name' => 'Coming soon', 'code' => 'coming-soon', 'active_from' => now()->addWeek()]);

        $this->laptop->related()->attach(Product::query()->where('code', 'laptop-air')->value('id'));
    }

    public function test_the_home_page_shows_every_module_with_its_latest_records(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<title>Demo shop</title>', false)
            ->assertSee('<a href="'.$this->root().'/catalog">Show all</a>', false)
            ->assertSeeInOrder(['Products', 'Laptop Pro', 'Laptop Air', 'Gift card', 'Posts'])
            ->assertDontSee('Retired')
            ->assertDontSee('Coming soon');
    }

    public function test_a_module_list_shows_top_sections_and_visible_records_in_order(): void
    {
        config(['atrium-theme.per_page' => 2]);

        $this->get('/catalog')
            ->assertOk()
            ->assertSee('<h1>Products</h1>', false)
            ->assertSee('href="'.$this->root().'/catalog/computers"', false)
            ->assertDontSee('Laptops</a>', false)
            ->assertDontSee('Hidden')
            ->assertSeeInOrder(['Laptop Pro', 'Laptop Air'])
            ->assertDontSee('Gift card')
            ->assertSee('Page 1 of 2')
            ->assertSee('<link rel="canonical" href="'.$this->root().'/catalog">', false);

        $this->get('/catalog?page=2')
            ->assertOk()
            ->assertSee('Gift card')
            ->assertSee('<link rel="canonical" href="'.$this->root().'/catalog?page=2">', false)
            ->assertSee('rel="prev"', false);
    }

    public function test_a_section_shows_its_text_subsections_and_records(): void
    {
        $this->get('/catalog/computers')
            ->assertOk()
            ->assertSee('<h1>Computers</h1>', false)
            ->assertSee('<p>Everything that computes.</p>', false)
            ->assertSee('href="'.$this->root().'/catalog/computers/laptops"', false)
            ->assertSee('<meta name="description" content="Everything that computes.">', false);

        $this->get('/catalog/computers/laptops')
            ->assertOk()
            ->assertSeeInOrder(['You are here', 'Products', 'Computers', '<h1>Laptops</h1>'], false)
            ->assertSee('Laptop Pro')
            ->assertSee('Laptop Air')
            ->assertDontSee('Gift card');
    }

    public function test_a_record_page_is_drawn_from_its_admin_fields(): void
    {
        $response = $this->get('/catalog/computers/laptops/laptop-pro')->assertOk();

        $response
            ->assertSee('<title>Laptop Pro | Demo shop</title>', false)
            ->assertSee('<h1>Laptop Pro</h1>', false)
            ->assertSee('<link rel="canonical" href="'.$this->root().'/catalog/computers/laptops/laptop-pro">', false)
            ->assertSee('<meta property="og:image" content="'.$this->root().'/upload/iblock/1/laptop.jpg">', false)
            ->assertSee('<meta name="description" content="Light &amp; fast. All day.">', false)
            ->assertSee('datetime="2026-03-01T10:00:00', false)
            // The detailed text wins over the preview, as HTML.
            ->assertSee('<p>The <strong>best</strong> laptop.</p>', false)
            // Properties in the form's order, with the select's label and the
            // directory's name instead of their stored values.
            ->assertSeeInOrder(['<dt>Article</dt>', '<dd>LP-001</dd>', '<dt>Price</dt>', '1999.5', '<dt>Color</dt>', '<dd>Silver</dd>', '<dt>Brand</dt>', '<dd>Northwind</dd>'], false)
            ->assertSee('<a href="'.$this->root().'/catalog/computers/laptops/laptop-air">Laptop Air</a>', false)
            ->assertSee('<h2>Specifications</h2>', false)
            ->assertSee('<li>16 GB</li>', false)
            ->assertSee('src="'.$this->root().'/upload/iblock/1/a.jpg"', false)
            ->assertSee('src="'.$this->root().'/storage/products/b.jpg"', false)
            ->assertSee('<a href="'.$this->root().'/upload/iblock/1/manual%20v2.pdf" download>manual v2.pdf</a>', false)
            // Flags, section pickers and SEO fields are not for readers.
            ->assertDontSee('Orderable')
            ->assertDontSee('<dt>Section</dt>', false)
            ->assertDontSee('Meta title');
    }

    public function test_plain_text_is_escaped_and_keeps_its_line_breaks(): void
    {
        $this->laptop->update(['detail_text' => null]);

        $this->get('/catalog/computers/laptops/laptop-pro')
            ->assertSee('Light &amp; fast.<br />', false);
    }

    public function test_an_old_address_leads_to_where_the_record_lives_now(): void
    {
        $this->get('/catalog/laptop-pro')->assertRedirect($this->root().'/catalog/computers/laptops/laptop-pro')->assertStatus(301);
        $this->get('/catalog/computers/laptop-pro')->assertStatus(301);
        $this->get('/catalog/laptops')->assertRedirect($this->root().'/catalog/computers/laptops');

        // A record outside any section lives right under the prefix.
        $this->get('/catalog/gift-card')->assertOk()->assertSee('<h1>Gift card</h1>', false);
    }

    public function test_hidden_and_unknown_records_are_not_found(): void
    {
        $this->get('/catalog/retired')->assertNotFound();
        $this->get('/catalog/coming-soon')->assertNotFound();
        $this->get('/catalog/hidden')->assertNotFound();
        $this->get('/catalog/nothing-here')->assertNotFound();
    }

    public function test_a_translated_module_has_a_page_in_every_language_it_is_written_in(): void
    {
        Language::query()->create(['code' => 'de', 'name' => 'Deutsch']);

        Post::query()->create([
            'name' => ['en' => 'Good morning', 'de' => 'Guten Morgen'],
            'code' => ['en' => 'good-morning', 'de' => 'guten-morgen'],
            'detail_text' => ['en' => '<p>Hello.</p>', 'de' => '<p>Hallo.</p>'],
        ]);
        Post::query()->create(['name' => ['en' => 'English only'], 'code' => ['en' => 'english-only']]);

        $this->get('/posts/good-morning')
            ->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('<link rel="alternate" hreflang="de" href="'.$this->root().'/de/posts/guten-morgen">', false)
            ->assertSee('<a href="'.$this->root().'/de/posts/guten-morgen" hreflang="de" lang="de"', false);

        $this->get('/de/posts/guten-morgen')
            ->assertOk()
            ->assertSee('<html lang="de">', false)
            ->assertSee('<h1>Guten Morgen</h1>', false)
            ->assertSee('<p>Hallo.</p>', false)
            ->assertSee('Dunkles Design');

        // No German version: no alternate, and the switch leads to the German
        // home page rather than to a 404.
        $this->get('/posts/english-only')
            ->assertOk()
            ->assertDontSee('hreflang="x-default"', false)
            ->assertSee('<a href="'.$this->root().'/de" hreflang="de"', false);

        $this->get('/de/posts/english-only')->assertNotFound();
        $this->get('/de/posts')->assertOk()->assertSee('Guten Morgen')->assertDontSee('English only');
    }

    public function test_the_sitemap_lists_the_pages_the_theme_draws(): void
    {
        Language::query()->create(['code' => 'de', 'name' => 'Deutsch']);
        Post::query()->create(['name' => ['en' => 'Hi', 'de' => 'Hallo'], 'code' => ['en' => 'hi', 'de' => 'hallo']]);

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringContainsString('<loc>'.$this->root().'/catalog/computers/laptops/laptop-pro</loc>', (string) $xml);
        $this->assertStringContainsString('<loc>'.$this->root().'/catalog/computers</loc>', (string) $xml);
        $this->assertStringContainsString('<loc>'.$this->root().'/de/posts/hallo</loc>', (string) $xml);
        // A product is not translated: one page, not one per language.
        $this->assertStringNotContainsString('/de/catalog/', (string) $xml);
        $this->assertStringNotContainsString('retired', (string) $xml);
    }
}
