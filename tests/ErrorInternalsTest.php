<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme\Tests;

use Atrium\Admin\Models\Site;
use Atrium\Admin\Support\Sites;
use Atrium\Theme\Errors\ErrorPages;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Exceptions\RegisterErrorViewPaths;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;

/**
 * The parts of the error pages the pages themselves do not show: which
 * requests are the admin's, what is known when the database is down, and
 * which view names the theme answers for.
 */
final class ErrorInternalsTest extends TestCase
{
    private string $views;

    protected function setUp(): void
    {
        parent::setUp();

        $this->views = sys_get_temp_dir().'/atrium-error-internals-'.uniqid();
        config(['view.paths' => [$this->views]]);
        (new RegisterErrorViewPaths)();
    }

    protected function tearDown(): void
    {
        (new Filesystem)->deleteDirectory($this->views);

        parent::tearDown();
    }

    private function request(string $uri): void
    {
        $this->app->instance('request', Request::create($uri));
    }

    /**
     * @return array<string,array{?string,string,bool}>
     */
    public static function prefixes(): array
    {
        return [
            'the default prefix, the admin' => [null, '/admin', false],
            'the default prefix, a screen of the admin' => [null, '/admin/users', false],
            'the default prefix, a visitor' => [null, '/administrators', true],
            'the default prefix, a page' => [null, '/about', true],
            'a prefix with slashes, the admin' => ['/panel/', '/panel', false],
            'a prefix with slashes, a screen' => ['/panel/', '/panel/users', false],
            'a prefix with slashes, a visitor' => ['/panel/', '/panelx', true],
            'a prefix with a slash behind' => ['panel/', '/panel/users', false],
            'a prefix with a slash before' => ['/panel', '/panel/users', false],
            'a prefix of a folder' => ['manage/panel', '/manage/panel/users', false],
            'the admin of another prefix is a visitor here' => ['/panel/', '/admin', true],
        ];
    }

    #[DataProvider('prefixes')]
    public function test_only_the_prefix_of_the_admin_is_not_the_visitors(?string $prefix, string $uri, bool $themed): void
    {
        if ($prefix !== null) {
            config(['atrium.prefix' => $prefix]);
        }

        $this->request($uri);

        $this->assertSame($themed, (new ErrorPages)->themed());
    }

    public function test_the_admin_keeps_the_site_picked_in_its_header(): void
    {
        $second = Site::query()->create(['code' => 'second', 'name' => 'Second', 'folder' => 'site2']);
        app(Sites::class)->flush();
        app(Sites::class)->setCurrent($second);

        $this->request('/admin/users');
        $this->assertSame('second', (new ErrorPages)->siteCode());

        $this->request('/anything');
        $this->assertSame('main', (new ErrorPages)->siteCode());
    }

    public function test_the_page_is_drawn_with_the_names_of_the_site(): void
    {
        Site::query()->where('code', 'main')->update(['name' => 'Main shop']);
        app(Sites::class)->flush();
        $this->request('/');

        $data = (new ErrorPages)->prepare();

        $this->assertSame('Main shop', $data['siteName']);
        $this->assertSame($this->root(), $data['homeUrl']);
        $this->assertNull($data['searchUrl']);
    }

    public function test_a_site_without_a_name_takes_the_name_of_the_app(): void
    {
        Site::query()->where('code', 'main')->update(['name' => '']);
        app(Sites::class)->flush();
        $this->request('/');

        $this->assertSame('Demo shop', (new ErrorPages)->prepare()['siteName']);

        config(['app.name' => null]);
        $this->assertSame('', (new ErrorPages)->prepare()['siteName']);
    }

    public function test_with_the_database_down_and_no_name_of_the_app_the_page_has_an_empty_one(): void
    {
        config(['app.name' => null]);
        $this->app->instance('request', new class extends Request
        {
            public function segment($index, $default = null): never
            {
                throw new RuntimeException('The database is down');
            }
        });

        $this->assertSame('', (new ErrorPages)->prepare()['siteName']);
    }

    public function test_with_the_database_down_the_page_has_what_is_known(): void
    {
        $this->app->instance('request', new class extends Request
        {
            public function segment($index, $default = null): never
            {
                throw new RuntimeException('The database is down');
            }
        });

        $this->assertSame(
            ['siteName' => 'Demo shop', 'homeUrl' => $this->root(), 'searchUrl' => null],
            (new ErrorPages)->prepare(),
        );
    }

    /**
     * @return array<string,array{string}>
     */
    public static function notCodes(): array
    {
        return [
            'four digits' => ['4040'],
            'a letter before' => ['x404'],
            'a letter after' => ['404x'],
            'a zero code' => ['004'],
            'a six hundred' => ['600'],
        ];
    }

    #[DataProvider('notCodes')]
    public function test_a_view_of_the_errors_that_is_no_status_code_is_not_the_sites(string $name): void
    {
        $this->page("main/{$name}", 'A page of the site');
        $this->page('4xx', 'Client error');
        $this->page('5xx', 'Server error');

        $this->assertFalse(View::exists("errors::{$name}"));
    }

    public function test_a_view_of_the_errors_that_is_a_status_code_is_found(): void
    {
        $this->page('main/404', 'Main 404');
        $this->page('main/100', 'Main 100');
        $this->page('main/599', 'Main 599');

        $this->assertTrue(View::exists('errors::404'));
        $this->assertTrue(View::exists('errors::599'));
        $this->assertTrue(View::exists('errors::100'));
        $this->assertFalse(View::exists('errors::600'));
    }

    /**
     * @return array<string,array{string,string,bool}>
     */
    public static function siteCodes(): array
    {
        return [
            'letters, digits, a dash and an underscore' => ['shop-2_b', 'shop-2_b/404', true],
            'a dot makes a folder of the code' => ['a.b', 'a/b/404', false],
            'a slash is not a code' => ['a/b', 'a/b/404', false],
            'a space is not a code' => ['a b', 'a b/404', false],
            'a code that only ends well' => ['x.shop', 'x/shop/404', false],
        ];
    }

    #[DataProvider('siteCodes')]
    public function test_only_a_plain_site_code_names_a_folder_of_pages(string $code, string $page, bool $found): void
    {
        $site = Site::query()->create(['code' => $code, 'name' => 'Odd', 'folder' => 'odd']);
        app(Sites::class)->flush();
        app(Sites::class)->setCurrent($site);
        $this->request('/admin/users');
        $this->page($page, 'The page of the site');

        $this->assertSame($found, str_contains(View::make('errors::404')->render(), 'The page of the site'));
    }

    private function page(string $name, string $text): void
    {
        $files = new Filesystem;
        $files->ensureDirectoryExists(dirname("{$this->views}/errors/{$name}"));
        $files->put("{$this->views}/errors/{$name}.blade.php", "<h1>{$text}</h1>");
    }
}
