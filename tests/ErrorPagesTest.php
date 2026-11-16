<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Models\Language;
use Atrium\Admin\Models\Site;
use Atrium\Admin\Support\ContentLanguages;
use Atrium\Admin\Support\Sites;
use Atrium\Theme\Errors\ErrorPages;
use Atrium\Theme\Errors\ErrorViewFinder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Events\LocaleUpdated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\View\FileViewFinder;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;

/**
 * Error pages: the app's own resources/views/errors first (per site, then
 * shared, then 4xx/5xx), the theme's next, Laravel's last; JSON stays JSON
 * and the admin keeps its own screens.
 */
final class ErrorPagesTest extends TestCase
{
    /** A views folder of the app's own, fresh for every test. */
    private string $views;

    protected function defineRoutes($router): void
    {
        $router->middleware('web')->group(function () use ($router): void {
            $router->get('fail/{code}', fn (string $code) => abort((int) $code));
            $router->get('crash', fn () => throw new RuntimeException('Something broke'));
        });
    }

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.debug' => false]);

        // Only this folder: the test app's own resources/views/errors has a
        // 503 page of its own.
        $this->views = sys_get_temp_dir().'/atrium-error-views-'.uniqid();
        config(['view.paths' => [$this->views]]);
    }

    protected function tearDown(): void
    {
        (new Filesystem)->deleteDirectory($this->views);

        parent::tearDown();
    }

    /**
     * @return array<int,array{0:int,1:string,2:string}>
     */
    public static function codes(): array
    {
        return [
            '403' => [403, 'fail/403', 'Access denied'],
            '404' => [404, 'no/such/page', 'Page not found'],
            '419' => [419, 'fail/419', 'Page expired'],
            '429' => [429, 'fail/429', 'Too many requests'],
            '500' => [500, 'crash', 'Something went wrong'],
            '503' => [503, 'fail/503', 'Back soon'],
        ];
    }

    #[DataProvider('codes')]
    public function test_the_theme_draws_each_error_page(int $code, string $path, string $title): void
    {
        $response = $this->get('/'.$path)
            ->assertStatus($code)
            ->assertSee('<p class="error-code">'.$code.'</p>', false)
            ->assertSee('<h1 id="error-title">'.$title.'</h1>', false)
            ->assertSee('<title>'.$title.' | Demo shop</title>', false)
            ->assertSee('<a class="site-name" href="'.$this->root().'">Demo shop</a>', false)
            ->assertSee('Go to the home page');

        // A client error keeps the site's menu; a server error does without
        // the layout and everything it reads from the database.
        $code < 500
            ? $response->assertSee('class="menu"', false)
            : $response->assertDontSee('class="menu"', false);
    }

    #[DataProvider('codes')]
    public function test_the_apps_own_page_wins_over_the_theme(int $code, string $path, string $title): void
    {
        $this->page("{$code}", "Our own {$code}");

        $this->get('/'.$path)
            ->assertStatus($code)
            ->assertSee("Our own {$code}")
            ->assertDontSee($title)
            ->assertDontSee('error-code');
    }

    public function test_a_page_of_the_site_wins_over_the_shared_one(): void
    {
        $this->page('main/404', 'Main site 404');
        $this->page('404', 'Shared 404');

        $this->get('/missing')->assertNotFound()->assertSee('Main site 404')->assertDontSee('Shared 404');
    }

    public function test_a_page_of_another_site_is_not_used(): void
    {
        Site::query()->create(['code' => 'second', 'name' => 'Second', 'folder' => 'site2']);
        $this->page('second/404', 'Second site 404');
        $this->page('404', 'Shared 404');

        $this->get('/missing')->assertNotFound()->assertSee('Shared 404')->assertDontSee('Second site 404');
        $this->get('/site2/missing')->assertNotFound()->assertSee('Second site 404');
    }

    public function test_a_site_code_that_is_not_a_folder_name_is_never_a_path(): void
    {
        Site::query()->create(['code' => '../errors', 'name' => 'Odd', 'folder' => 'odd']);
        $this->page('404', 'Shared 404');

        $this->get('/odd/missing')->assertNotFound()->assertSee('Shared 404');
    }

    public function test_the_sites_4xx_wins_over_the_shared_404(): void
    {
        $this->page('main/4xx', 'Main site client error');
        $this->page('404', 'Shared 404');

        $this->get('/missing')->assertNotFound()->assertSee('Main site client error');
    }

    public function test_the_apps_4xx_and_5xx_win_over_the_themes_pages(): void
    {
        $this->page('4xx', 'Our client error');
        $this->page('5xx', 'Our server error');

        $this->get('/missing')->assertNotFound()->assertSee('Our client error')->assertDontSee('error-code');
        $this->get('/crash')->assertStatus(500)->assertSee('Our server error');
    }

    public function test_a_code_without_a_page_of_its_own_gets_the_themes_4xx_or_5xx(): void
    {
        $this->get('/fail/418')
            ->assertStatus(418)
            ->assertSee('<p class="error-code">418</p>', false)
            ->assertSee('The request could not be completed');

        $this->get('/fail/502')
            ->assertStatus(502)
            ->assertSee('<p class="error-code">502</p>', false)
            ->assertSee('The server could not answer')
            ->assertDontSee('class="menu"', false);
    }

    public function test_json_requests_keep_json_errors(): void
    {
        $this->getJson('/missing')
            ->assertNotFound()
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonStructure(['message']);

        $this->getJson('/crash')
            ->assertStatus(500)
            ->assertExactJson(['message' => 'Server Error']);
    }

    public function test_the_admin_keeps_laravels_pages(): void
    {
        $this->get('/admin/no-such-screen')
            ->assertNotFound()
            ->assertDontSee('error-code')
            ->assertSee('Not Found');

        $this->get('/admin/no-such-screen', ['X-Inertia' => 'true'])
            ->assertNotFound()
            ->assertDontSee('error-code');
    }

    public function test_the_apps_own_page_still_applies_in_the_admin(): void
    {
        $this->page('404', 'Shared 404');

        $this->get('/admin/no-such-screen')->assertNotFound()->assertSee('Shared 404');
    }

    public function test_the_500_page_draws_with_the_database_down(): void
    {
        $default = config('database.default');
        config([
            'database.connections.broken' => ['driver' => 'sqlite', 'database' => $this->views.'/missing.sqlite', 'prefix' => ''],
            'database.default' => 'broken',
        ]);
        app(Sites::class)->flush();
        app(ContentLanguages::class)->flush();

        try {
            $this->get('/crash')
                ->assertStatus(500)
                ->assertSee('<p class="error-code">500</p>', false)
                ->assertSee('<a class="site-name" href="'.$this->root().'">Demo shop</a>', false);

            $this->get('/missing')
                ->assertNotFound()
                ->assertSee('<p class="error-code">404</p>', false);
        } finally {
            config(['database.default' => $default]);
        }
    }

    public function test_a_client_error_falls_back_to_the_plain_frame_when_the_layout_fails(): void
    {
        View::composer('atrium-theme::layout', fn () => throw new RuntimeException('The menu is broken'));

        $this->get('/missing')
            ->assertNotFound()
            ->assertSee('<p class="error-code">404</p>', false)
            ->assertSee('<a class="site-name" href="'.$this->root().'">Demo shop</a>', false)
            ->assertDontSee('class="menu"', false);
    }

    public function test_the_page_draws_when_the_site_cannot_be_looked_up(): void
    {
        Event::listen(LocaleUpdated::class, fn () => throw new RuntimeException('No languages'));

        $this->get('/missing')
            ->assertNotFound()
            ->assertSee('<p class="error-code">404</p>', false)
            ->assertSee('<a href="'.$this->root().'">Go to the home page</a>', false);
    }

    public function test_the_page_speaks_the_language_of_the_address(): void
    {
        Language::query()->create(['code' => 'de', 'name' => 'Deutsch']);

        $this->get('/de/gibt/es/nicht')
            ->assertNotFound()
            ->assertSee('<html lang="de">', false)
            ->assertSee('Seite nicht gefunden')
            ->assertSee('<a href="'.$this->root().'/de">Zur Startseite</a>', false);

        $this->get('/missing')->assertNotFound()->assertSee('<html lang="en">', false)->assertSee('Page not found');

        // Not a language of the site: the site's default language.
        $this->get('/ru/missing')->assertNotFound()->assertSee('Page not found');
    }

    public function test_the_page_speaks_the_language_of_the_site(): void
    {
        Language::query()->create(['code' => 'ru', 'name' => 'Русский']);
        Site::query()->create(['code' => 'second', 'name' => 'Второй', 'folder' => 'site2', 'languages' => ['ru'], 'default_language' => 'ru']);

        $this->get('/site2/missing')
            ->assertNotFound()
            ->assertSee('Страница не найдена')
            ->assertSee('Второй');
    }

    public function test_the_404_page_has_a_search_box_when_the_site_has_a_search(): void
    {
        $this->get('/missing')->assertDontSee('role="search"', false);

        Route::middleware('web')->get('search', fn () => 'results')->name('atrium.search');
        Route::getRoutes()->refreshNameLookups();

        $this->get('/missing')
            ->assertNotFound()
            ->assertSee('<form class="error-search" role="search" method="get" action="'.$this->root().'/search">', false)
            ->assertSee('<input id="error-search" type="search" name="q" maxlength="200">', false);
    }

    public function test_the_publish_tag_copies_the_pages_into_the_app(): void
    {
        $target = resource_path('views/errors');
        $files = new Filesystem;
        // The test app ships a 503 page of its own: out of the way for now.
        $backup = $this->views.'/skeleton-errors';
        $files->ensureDirectoryExists($this->views);
        $moved = $files->isDirectory($target) && $files->moveDirectory($target, $backup);

        try {
            $this->artisan('vendor:publish', ['--tag' => 'atrium-errors'])->assertSuccessful();

            foreach (['403', '404', '419', '429', '500', '503', '4xx', '5xx'] as $page) {
                $this->assertFileEquals(__DIR__."/../resources/views/errors/{$page}.blade.php", "{$target}/{$page}.blade.php");
            }

            // The copy is the app's own now: an edit shows, and a theme
            // update never touches it.
            $files->put("{$target}/404.blade.php", str_replace('<p>{{', '<p>Edited. {{', $files->get("{$target}/404.blade.php")));

            config(['view.paths' => [resource_path('views')]]);
            $this->get('/missing')->assertNotFound()->assertSee('Edited. The address may be mistyped');
        } finally {
            $files->deleteDirectory($target);
            $moved && $files->moveDirectory($backup, $target);
        }
    }

    public function test_maintenance_mode_prerenders_the_themes_503(): void
    {
        try {
            $this->artisan('down', ['--render' => 'errors::503'])->assertSuccessful();

            $this->get('/')
                ->assertStatus(503)
                ->assertSee('<p class="error-code">503</p>', false)
                ->assertSee('Back soon');
        } finally {
            $this->artisan('up');
        }
    }

    public function test_every_language_has_the_keys_of_english(): void
    {
        $english = require __DIR__.'/../lang/en/errors.php';
        $locales = glob(__DIR__.'/../lang/*', GLOB_ONLYDIR) ?: [];
        $this->assertCount(15, $locales);

        foreach ($locales as $locale) {
            $strings = require $locale.'/errors.php';
            $this->assertSame(array_keys($english), array_keys($strings), basename($locale));

            foreach ($english as $key => $value) {
                if (is_array($value)) {
                    $this->assertSame(array_keys($value), array_keys($strings[$key]), basename($locale).": {$key}");
                }
            }
        }
    }

    public function test_a_finder_another_package_put_in_place_is_left_alone(): void
    {
        $factory = View::getFacadeRoot();
        $theirs = new class(app('files'), []) extends FileViewFinder {};
        $ours = $factory->getFinder();

        try {
            $factory->setFinder($theirs);
            ErrorViewFinder::install($factory, new ErrorPages);

            $this->assertSame($theirs, $factory->getFinder());
            $this->assertInstanceOf(ErrorViewFinder::class, $ours);
        } finally {
            $factory->setFinder($ours);
        }
    }

    private function page(string $name, string $text): void
    {
        $files = new Filesystem;
        $files->ensureDirectoryExists(dirname("{$this->views}/errors/{$name}"));
        $files->put("{$this->views}/errors/{$name}.blade.php", "<h1>{$text}</h1>");
    }
}
