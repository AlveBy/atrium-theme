<?php

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Models\Language;
use Atrium\Admin\Models\Site;
use Atrium\Theme\Tests\Fixtures\Post;
use Atrium\Theme\Tests\Fixtures\Product;
use Atrium\Theme\Tests\Fixtures\ProductSection;

/**
 * A site in a folder of the domain (/site2/): every address the theme
 * draws stays inside the folder.
 */
final class FolderSiteTest extends TestCase
{
    private string $site2;

    protected function setUp(): void
    {
        parent::setUp();

        Language::query()->create(['code' => 'de', 'name' => 'Deutsch']);
        Site::query()->create(['code' => 'second', 'name' => 'Second', 'folder' => 'site2']);
        $this->site2 = $this->root().'/site2';

        $computers = ProductSection::query()->create(['name' => 'Computers', 'code' => 'computers']);
        Product::query()->create(['section_id' => $computers->id, 'name' => 'Laptop Pro', 'code' => 'laptop-pro', 'sort' => 100]);
        Product::query()->create(['name' => 'Gift card', 'code' => 'gift-card', 'sort' => 200]);
        Product::query()->create(['name' => 'Mouse', 'code' => 'mouse', 'sort' => 300]);

        Post::query()->create([
            'name' => ['en' => 'Good morning', 'de' => 'Guten Morgen'],
            'code' => ['en' => 'good-morning', 'de' => 'guten-morgen'],
        ]);
    }

    public function test_links_and_the_canonical_address_stay_inside_the_folder(): void
    {
        $this->get('/site2')
            ->assertOk()
            ->assertSee('<a href="'.$this->site2.'/catalog">Show all</a>', false)
            ->assertSee('<link rel="canonical" href="'.$this->site2.'">', false);

        $this->get('/site2/catalog/computers/laptop-pro')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.$this->site2.'/catalog/computers/laptop-pro">', false)
            ->assertSee('href="'.$this->site2.'/catalog"', false)
            ->assertSee('href="'.$this->site2.'/catalog/computers"', false);
    }

    public function test_pagination_stays_inside_the_folder(): void
    {
        config(['atrium-theme.per_page' => 2]);

        $this->get('/site2/catalog')
            ->assertOk()
            ->assertSee('href="'.$this->site2.'/catalog?page=2"', false);

        $this->get('/site2/catalog?page=2')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.$this->site2.'/catalog?page=2">', false)
            ->assertSee('href="'.$this->site2.'/catalog?page=1"', false);
    }

    public function test_language_versions_live_after_the_folder(): void
    {
        $this->get('/site2/posts/good-morning')
            ->assertOk()
            ->assertSee('<link rel="alternate" hreflang="de" href="'.$this->site2.'/de/posts/guten-morgen">', false);

        $this->get('/site2/de/posts/guten-morgen')
            ->assertOk()
            ->assertSee('<html lang="de">', false)
            ->assertSee('<link rel="alternate" hreflang="en" href="'.$this->site2.'/posts/good-morning">', false)
            ->assertSee('<link rel="canonical" href="'.$this->site2.'/de/posts/guten-morgen">', false);
    }

    public function test_an_old_address_redirects_inside_the_folder(): void
    {
        $this->get('/site2/catalog/laptop-pro')
            ->assertStatus(301)
            ->assertRedirect($this->site2.'/catalog/computers/laptop-pro');
    }

    public function test_the_folder_site_has_its_own_sitemap(): void
    {
        $xml = (string) $this->get('/site2/sitemap.xml')->assertOk()->getContent();

        $this->assertStringContainsString('<loc>'.$this->site2.'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.$this->site2.'/catalog/computers/laptop-pro</loc>', $xml);
        $this->assertStringContainsString('<loc>'.$this->site2.'/de/posts/guten-morgen</loc>', $xml);
        $this->assertStringContainsString('hreflang="en" href="'.$this->site2.'/posts/good-morning"', $xml);

        // The root site's sitemap has its own addresses, not the folder's.
        $root = (string) $this->get('/sitemap.xml')->assertOk()->getContent();
        $this->assertStringContainsString('<loc>'.$this->root().'/catalog/computers/laptop-pro</loc>', $root);
        $this->assertStringNotContainsString('/site2', $root);
    }
}
