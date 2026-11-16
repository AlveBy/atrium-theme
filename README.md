# Atrium theme

A neutral public site for [Atrium](https://github.com/AlveBy/atrium). It draws every module that has a public address, including iblocks just moved from 1C-Bitrix, without a template per module: a page is built from the fields of the module's admin form.

Light and dark, readable with any kind of colour vision, in the 15 languages of the admin.

## Install

```bash
composer require alveby/atrium-theme
```

The theme is on as soon as the package is installed. Tell it which modules have pages in `config/atrium.php` → `public_urls`:

```php
'public_urls' => [
    App\Models\News::class => ['prefix' => 'news'],
    App\Models\Product::class => ['prefix' => 'catalog', 'tree' => App\Models\ProductSection::class, 'parent' => 'section_id'],
    App\Models\ProductSection::class => ['prefix' => 'catalog', 'tree' => App\Models\ProductSection::class, 'parent' => 'parent_id'],
],
```

That gives:

| Address | Page |
|---|---|
| `/` | the latest records of every module |
| `/news`, `/catalog` | a module's list: top sections, then records, 20 per page |
| `/catalog/computers/laptops` | a section: its text, subsections and records |
| `/catalog/computers/laptops/laptop-pro` | a record |
| `/de/…` | the same in another content language |

Each module is a menu item, labelled with its admin label.

## What a visitor sees

- Only active records inside their active period (`active`, `active_from`, `active_to`, when the table has them), in the editors' order: `sort`, then the newest first, then the id.
- A record page shows the name, the date, the detail picture, the detail text (the preview text when there is none) and then the other fields in the order of the admin form: properties as a list of names and values, HTML fields as HTML, galleries as pictures, files as download links. Select values show their labels, links to other records show their names and lead to their pages.
- Service fields stay hidden: flags, section pickers, colours, maps, slugs and the SEO tab.
- A record reached by an old address (moved to another section, or linked without the section) gets a 301 to where it lives now. An unknown or hidden one gets a 404.
- Title, description, keywords and the Open Graph picture come from the record's SEO fields when the model uses `HasAdminSeo`, otherwise from its name and preview.
- Every page has a canonical address and `hreflang` alternates; the second and later pages of a list are canonical on their own.
- Translatable modules have a page in each language the record is written in. The language switcher leads to the same record in another language, or to that language's home page when there is no translation.
- The theme's pages go into `sitemap.xml`. A record that is not translated has one address, in the default language.

## Your own routes win

The theme registers its routes after the app's. A route you define yourself is matched first, and the home page is left to you when the app has its own `GET /`.

To switch the theme's routes off and keep only yours:

```dotenv
ATRIUM_THEME_ROUTES=false
```

Other settings (`php artisan vendor:publish --tag=atrium-theme-config`):

| Key | Default | |
|---|---|---|
| `routes` | `true` | register the pages |
| `per_page` | `20` | records per page in a list |
| `home_items` | `3` | records of each module on the home page |

## Change the look

Copy the templates and edit them:

```bash
php artisan vendor:publish --tag=atrium-theme-views
```

They land in `resources/views/vendor/atrium-theme`: `layout`, `home`, `list`, `page`, the partials, and two components you can use in your own templates:

```blade
<x-atrium-theme::language-switcher :urls="$urls" />
<x-atrium-theme::theme-toggle />
```

The layout already prints the site's counters and codes (`<x-atrium::head />`, `<x-atrium::body-start />`, `<x-atrium::body-end />`) and `hreflang`.

## Colours

The stylesheet is inline, so there is nothing to build or publish. Every colour is a custom property; override them in a stylesheet of your own after the theme's.

| Property | Light | Dark | Contrast on background / surface |
|---|---|---|---|
| `--bg` | `#ffffff` | `#121418` | |
| `--surface` | `#f3f4f6` | `#1d2026` | |
| `--text` | `#16181d` | `#e9ebef` | 17.8 / 16.1 · 15.5 / 13.7 |
| `--muted` | `#4f5662` | `#a9b0bb` | 7.4 / 6.7 · 8.4 / 7.5 |
| `--accent` (links) | `#1d4ed8` | `#8db4ff` | 6.7 / 6.1 · 8.9 / 7.9 |
| `--control` (borders of buttons) | `#6b7280` | `#858d99` | 4.8 / 4.4 · 5.5 / 4.9 |
| `--focus` | `#1d4ed8` | `#8db4ff` | 6.7 / 6.1 · 8.9 / 7.9 |
| `--line` (decorative) | `#e2e5ea` | `#2c3038` | |

Text keeps at least 4.5:1 (WCAG AA), controls and the focus ring at least 3:1; `tests/ContrastTest.php` checks it, so a change that breaks it fails CI. Links are always underlined and the current menu item is bold, so nothing depends on colour alone.

The theme follows the system setting until the visitor presses the switch; the choice is kept in `localStorage` (`atrium-theme`) and applied before the page is painted.

## Tests

From the repository root:

```bash
vendor/bin/phpunit packages/theme/tests
```

The tests set up a made-up catalog (sections, products with every field type, a translated blog) and check the pages, addresses, redirects, languages and the sitemap on SQLite, MySQL and PostgreSQL.

## License

MIT
