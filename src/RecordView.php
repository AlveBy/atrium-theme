<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

namespace Atrium\Theme;

use Atrium\Admin\Cache\Card;
use Atrium\Admin\Fields\BelongsToField;
use Atrium\Admin\Fields\BelongsToManyField;
use Atrium\Admin\Fields\Field;
use Atrium\Admin\Support\Cdn;
use Atrium\Admin\Support\MediaUrl;
use Atrium\Admin\Support\PublicUrl;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Throwable;

/**
 * A record as the page shows it, worked out from the fields of its admin
 * form: the picture, the text, then every other filled-in field by its type
 * — images as a gallery, files as links, the rest as a list of properties.
 * No template per module: whatever the form has, the page has.
 *
 * For a model in the record cache all of it is worked out once, when its
 * card is built (toArray(), stored as the card's "theme" part), and the
 * page reads it back with no query: fromCard().
 */
final class RecordView
{
    /**
     * Shown in their own places on the page, or not meant for visitors.
     */
    private const OWN_PLACE = [
        'name', 'code', 'active', 'active_from', 'active_to', 'sort', 'site_id',
        'preview_picture', 'detail_picture', 'preview_text', 'detail_text', 'description', 'picture',
        'created_at', 'updated_at', 'created_by', 'updated_by', 'parent_id', 'section_id',
    ];

    /**
     * Flags, coordinates, colours and section pickers mean something to
     * editors and templates, not to a reader.
     */
    private const NOT_SHOWN = ['slug', 'checkbox', 'map', 'treeSelect', 'color'];

    /** @var array<int|string,Field>|null */
    private ?array $fields = null;

    /**
     * @param  array<string,mixed>|null  $data  what toArray() gave when the card was built
     */
    public function __construct(
        public readonly Model $record,
        private readonly Pages $pages,
        private readonly ?array $data = null,
    ) {}

    /**
     * The page of a cached record: the card's model and the card's "theme"
     * part. A card built before the theme was installed has none, and the
     * page is worked out from the model then.
     */
    public static function fromCard(Card $card, Pages $pages): self
    {
        $data = $card->extra('theme');

        return new self($card->model(), $pages, is_array($data) ? $data : null);
    }

    /**
     * Everything the page shows, as plain values for the cache. Addresses
     * on the current host are kept without it: the same card serves every
     * domain of the site.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        $local = static fn (?string $url): ?string => self::local($url);

        return [
            'title' => $this->title(),
            'seo_title' => $this->seoTitle(),
            'seo_description' => $this->seoDescription(),
            'seo_keywords' => $this->seoKeywords(),
            'og_image' => $local($this->ogImage()),
            'url' => $local($this->url()),
            'path' => $this->pages->path($this->record),
            'urls' => array_map($local, $this->pages->urls($this->record)),
            'date' => $this->date()?->toIso8601String(),
            'image' => $local($this->image()),
            'thumbnail' => $local($this->thumbnail()),
            'teaser' => $this->teaser()?->toHtml(),
            'body' => $this->body()?->toHtml(),
            'blocks' => array_map(static function (array $block) use ($local): array {
                if (isset($block['html'])) {
                    $block['html'] = $block['html']->toHtml();
                }

                if (isset($block['images'])) {
                    $block['images'] = array_map($local, $block['images']);
                }

                if (isset($block['files'])) {
                    $block['files'] = array_map(static fn (array $file): array => ['name' => $file['name'], 'url' => $local($file['url'])], $block['files']);
                }

                if (isset($block['items'])) {
                    $block['items'] = array_map(static function (array $item) use ($local): array {
                        $item['url'] = $local($item['url'] ?? null);
                        $item['links'] = array_map(static fn (array $link): array => ['value' => $link['value'], 'url' => $local($link['url'] ?? null)], $item['links']);

                        return $item;
                    }, $block['items']);
                }

                return $block;
            }, $this->blocks()),
        ];
    }

    /**
     * The path below the module's prefix (section path and code).
     */
    public function path(): ?string
    {
        if ($this->data !== null) {
            return $this->data['path'] ?? null;
        }

        return $this->pages->path($this->record);
    }

    /**
     * The page in every language it is written in.
     *
     * @return array<string,string>
     */
    public function urls(): array
    {
        if ($this->data !== null) {
            return array_filter(array_map(self::absolute(...), (array) ($this->data['urls'] ?? [])));
        }

        return $this->pages->urls($this->record);
    }

    public function title(): string
    {
        if ($this->data !== null) {
            return (string) $this->data['title'];
        }

        foreach (['name', 'title'] as $attribute) {
            if (($text = $this->pages->text($this->record, $attribute)) !== '') {
                return $text;
            }
        }

        // A translated record without a name in this language still has one
        // in the default language.
        return (string) ($this->record->getAttribute('name') ?: '#'.$this->record->getKey());
    }

    /**
     * The page title for search engines: the record's SEO title when the
     * module keeps one (HasAdminSeo), the name otherwise.
     */
    public function seoTitle(): string
    {
        if ($this->data !== null) {
            return (string) $this->data['seo_title'];
        }

        $title = method_exists($this->record, 'getSeoTitle') ? (string) $this->record->getSeoTitle() : '';

        return $title !== '' ? $title : $this->title();
    }

    public function seoDescription(): string
    {
        if ($this->data !== null) {
            return (string) $this->data['seo_description'];
        }

        $description = method_exists($this->record, 'getSeoDescription') ? (string) $this->record->getSeoDescription() : '';

        if ($description !== '') {
            return $description;
        }

        $text = html_entity_decode(strip_tags((string) ($this->teaser() ?? $this->body())), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return Str::limit(trim((string) preg_replace('/\s+/u', ' ', $text)), 300, '…', preserveWords: true);
    }

    public function seoKeywords(): ?string
    {
        if ($this->data !== null) {
            return $this->data['seo_keywords'] ?? null;
        }

        return method_exists($this->record, 'getSeoKeywords') ? $this->record->getSeoKeywords() : null;
    }

    public function ogImage(): ?string
    {
        if ($this->data !== null) {
            return self::absolute($this->data['og_image'] ?? null);
        }

        $image = method_exists($this->record, 'getOgImage') ? $this->record->getOgImage() : null;

        return is_string($image) && $image !== '' ? $image : $this->image();
    }

    public function url(): ?string
    {
        if ($this->data !== null) {
            return self::absolute($this->data['url'] ?? null);
        }

        return $this->pages->url($this->record);
    }

    public function date(): ?Carbon
    {
        if ($this->data !== null) {
            return isset($this->data['date']) ? Carbon::parse($this->data['date'])->locale(app()->getLocale()) : null;
        }

        $value = $this->record->getAttribute('active_from');

        return $value instanceof DateTimeInterface ? Carbon::instance($value)->locale(app()->getLocale()) : null;
    }

    /**
     * The big picture of the page.
     */
    public function image(): ?string
    {
        if ($this->data !== null) {
            return self::absolute($this->data['image'] ?? null);
        }

        return $this->media('detail_picture') ?? $this->media('picture') ?? $this->media('preview_picture');
    }

    /**
     * The small picture of a card in a list.
     */
    public function thumbnail(): ?string
    {
        if ($this->data !== null) {
            return self::absolute($this->data['thumbnail'] ?? null);
        }

        return $this->media('preview_picture') ?? $this->media('picture') ?? $this->media('detail_picture');
    }

    /**
     * The short text of a card in a list.
     */
    public function teaser(): ?HtmlString
    {
        if ($this->data !== null) {
            return isset($this->data['teaser']) ? new HtmlString((string) $this->data['teaser']) : null;
        }

        return $this->html('preview_text');
    }

    /**
     * The main text: the detailed one, else the preview, else a section's
     * description.
     */
    public function body(): ?HtmlString
    {
        if ($this->data !== null) {
            return isset($this->data['body']) ? new HtmlString((string) $this->data['body']) : null;
        }

        return $this->html('detail_text') ?? $this->html('preview_text') ?? $this->html('description');
    }

    /**
     * Everything else that is filled in, in the form's order. Neighbouring
     * properties are grouped into one list.
     *
     * @return array<int,array{kind:'properties',items:array<int,array{label:string,value:string,url:?string,links:array<int,array{value:string,url:?string}>}>}|array{kind:'html',label:string,html:HtmlString}|array{kind:'images',label:string,images:array<int,string>}|array{kind:'files',label:string,files:array<int,array{name:string,url:string}>}>
     */
    public function blocks(): array
    {
        if ($this->data !== null) {
            return $this->cachedBlocks();
        }

        $blocks = [];
        $skip = $this->skippedColumns();

        foreach ($this->fields() as $field) {
            $name = $field->getName();
            $type = $field->getType();

            if (in_array($name, $skip, true) || in_array($type, self::NOT_SHOWN, true)
                || $field->getTab() === 'seo' || str_starts_with($name, 'meta_')) {
                continue;
            }

            $block = $this->block($field);

            if ($block === null) {
                continue;
            }

            if ($block['kind'] === 'property') {
                $last = array_key_last($blocks);

                if ($last === null || $blocks[$last]['kind'] !== 'properties') {
                    $blocks[] = ['kind' => 'properties', 'items' => []];
                    $last = array_key_last($blocks);
                }

                $blocks[$last]['items'][] = ['label' => $block['label'], 'value' => $block['value'], 'url' => $block['url'], 'links' => $block['links'] ?? []];

                continue;
            }

            $blocks[] = $block;
        }

        return $blocks;
    }

    /**
     * @return array<string,mixed>|null
     */
    private function block(Field $field): ?array
    {
        $label = $field->getLabel();
        $type = $field->getType();

        if ($field instanceof BelongsToManyField) {
            return $this->linksBlock($field);
        }

        $value = $this->record->getAttribute($field->getColumn());

        if ($value === null || $value === '' || $value === []) {
            return null;
        }

        return match ($type) {
            'image', 'gallery' => ($images = array_values(array_filter(array_map(MediaUrl::to(...), $this->list($value))))) === []
                ? null
                : ['kind' => 'images', 'label' => $label, 'images' => $images],
            'file' => ($files = $this->files($value)) === [] ? null : ['kind' => 'files', 'label' => $label, 'files' => $files],
            'content', 'wysiwyg' => ($html = $this->html($field->getColumn())) === null ? null : ['kind' => 'html', 'label' => $label, 'html' => $html],
            'textarea' => ['kind' => 'html', 'label' => $label, 'html' => new HtmlString(nl2br(e((string) $value)))],
            'belongsTo' => $this->linkProperty($field, $value),
            default => ($text = $this->text($field, $value)) === '' ? null : ['kind' => 'property', 'label' => $label, 'value' => $text, 'url' => null],
        };
    }

    private function text(Field $field, mixed $value): string
    {
        $values = $this->list($value);
        $props = in_array($field->getType(), ['select', 'multiSelect'], true) ? $field->toArray() : [];

        if (isset($props['options']) && is_array($props['options'])) {
            $labels = [];

            foreach ($props['options'] as $option) {
                if (array_key_exists('value', $option)) {
                    $labels[(string) $option['value']] = $option['label'] ?? $option['value'];
                }
            }

            $values = array_map(static fn (mixed $v): mixed => is_scalar($v) ? ($labels[(string) $v] ?? $v) : $v, $values);
        }

        $values = array_map(fn (mixed $v): string => $this->scalar($field->getType(), $v), $values);

        return implode(', ', array_filter($values, static fn (string $v): bool => $v !== ''));
    }

    private function scalar(string $type, mixed $value): string
    {
        if ($value instanceof DateTimeInterface || (in_array($type, ['date', 'dateTime', 'datetime'], true) && is_string($value) && $value !== '')) {
            try {
                $date = Carbon::parse($value)->locale(app()->getLocale());

                return $type === 'date' ? $date->isoFormat('LL') : $date->isoFormat('LLL');
            } catch (Throwable) {
                // Not a date anybody can read: shown as it is, below.
            }
        }

        if (is_array($value)) {
            return implode(', ', array_filter(array_map(fn (mixed $v): string => $this->scalar($type, $v), $value)));
        }

        return is_scalar($value) ? (is_bool($value) ? '' : (string) $value) : '';
    }

    /**
     * @return array<string,mixed>|null
     */
    private function linkProperty(Field $field, mixed $value): ?array
    {
        if (! $field instanceof BelongsToField || ($class = $field->getModelClass()) === null || ! is_scalar($value)) {
            return null;
        }

        // The relation behind the field, when it is loaded (cards load it
        // for every record at once); a query otherwise.
        $relation = Str::camel(Str::beforeLast($field->getColumn(), '_id'));
        $loaded = $field->getValueColumn() === 'id' && $relation !== $field->getColumn() && $this->record->relationLoaded($relation)
            ? $this->record->getRelation($relation)
            : false;

        /** @var class-string<Model> $class */
        $related = $loaded instanceof Model || $loaded === null
            ? $loaded
            : $this->pages->linked($class, $field->getValueColumn(), $value);

        if ($related === null) {
            return null;
        }

        $text = $this->pages->text($related, $field->getDisplayColumn());

        return $text === '' ? null : [
            'kind' => 'property',
            'label' => $field->getLabel(),
            'value' => $text,
            'url' => $this->linkTo($related),
        ];
    }

    /**
     * @return array<string,mixed>|null
     */
    private function linksBlock(BelongsToManyField $field): ?array
    {
        $name = $field->getName();

        if (! method_exists($this->record, $name) || ! ($relation = $this->record->{$name}()) instanceof BelongsToMany) {
            return null;
        }

        $items = [];

        $related = $this->record->relationLoaded($name) ? $this->record->getRelation($name) : $relation->get();

        foreach ($related as $item) {
            $text = $this->pages->text($item, $field->getDisplayColumn());

            if ($text !== '') {
                $items[] = ['value' => $text, 'url' => $this->linkTo($item)];
            }
        }

        if ($items === []) {
            return null;
        }

        return [
            'kind' => 'property',
            'label' => $field->getLabel(),
            'value' => implode(', ', array_column($items, 'value')),
            'url' => null,
            // Each related record is a link of its own.
            'links' => $items,
        ];
    }

    /**
     * A linked record's page, if it has one a visitor may open.
     */
    private function linkTo(Model $related): ?string
    {
        if (PublicUrl::ruleFor($related::class) === null
            || (isset($this->pages->columns($related)['active']) && ! $related->getAttribute('active'))) {
            return null;
        }

        return $this->pages->url($related);
    }

    /**
     * @return array<int,array{name:string,url:string}>
     */
    private function files(mixed $value): array
    {
        $files = [];

        foreach ($this->list($value) as $file) {
            $url = MediaUrl::to($file);

            if ($url !== null) {
                $name = is_array($file) && isset($file['name']) ? (string) $file['name'] : rawurldecode(basename((string) parse_url($url, PHP_URL_PATH)));
                $files[] = ['name' => $name, 'url' => $url];
            }
        }

        return $files;
    }

    /**
     * A value that may be one item, a list, or a JSON list.
     *
     * @return array<int|string,mixed>
     */
    private function list(mixed $value): array
    {
        if (is_string($value) && str_starts_with(ltrim($value), '[')) {
            $decoded = json_decode($value, true);

            if (is_array($decoded)) {
                return $decoded;
            }
        }

        if (is_array($value) && (array_is_list($value) || ! (isset($value['path']) || isset($value['url'])))) {
            return $value;
        }

        return [$value];
    }

    private function media(string $attribute): ?string
    {
        return MediaUrl::to($this->record->getAttribute($attribute));
    }

    /**
     * A text column as HTML. Bitrix keeps a `_type` beside the text: plain
     * text there is escaped and keeps its line breaks.
     */
    private function html(string $attribute): ?HtmlString
    {
        $text = $this->pages->text($this->record, $attribute);

        if (trim(strip_tags($text, '<img><iframe><video><table>')) === '') {
            return null;
        }

        // Pictures and files inside the text come through the CDN too.
        return new HtmlString($this->record->getAttribute($attribute.'_type') === 'text' ? nl2br(e($text)) : Cdn::html($text));
    }

    /**
     * The section column of the record and its own columns shown elsewhere.
     *
     * @return array<int,string>
     */
    private function skippedColumns(): array
    {
        $parent = PublicUrl::ruleFor($this->record::class)['parent'] ?? null;

        return $parent === null ? self::OWN_PLACE : [...self::OWN_PLACE, $parent];
    }

    /**
     * @return array<int|string,Field>
     */
    private function fields(): array
    {
        $class = $this->record::class;
        $fields = method_exists($class, 'getAdministrationFields') ? $class::getAdministrationFields() : [];

        return $this->fields ??= array_filter($fields, static fn (mixed $field): bool => $field instanceof Field);
    }

    /**
     * Blocks read back from the cache: texts become HTML again, addresses
     * get the current host.
     *
     * @return array<int,array<string,mixed>>
     */
    private function cachedBlocks(): array
    {
        return array_map(static function (array $block): array {
            if (isset($block['html'])) {
                $block['html'] = new HtmlString((string) $block['html']);
            }

            if (isset($block['images'])) {
                $block['images'] = array_map(self::absolute(...), $block['images']);
            }

            if (isset($block['files'])) {
                $block['files'] = array_map(static fn (array $file): array => ['name' => $file['name'], 'url' => self::absolute($file['url'])], $block['files']);
            }

            if (isset($block['items'])) {
                $block['items'] = array_map(static function (array $item): array {
                    $item['url'] = self::absolute($item['url'] ?? null);
                    $item['links'] = array_map(static fn (array $link): array => ['value' => $link['value'], 'url' => self::absolute($link['url'] ?? null)], $item['links'] ?? []);

                    return $item;
                }, $block['items']);
            }

            return $block;
        }, (array) ($this->data['blocks'] ?? []));
    }

    private static function local(?string $url): ?string
    {
        if ($url === null) {
            return null;
        }

        $host = request()->getSchemeAndHttpHost();

        return str_starts_with($url, $host.'/') || $url === $host ? substr($url, strlen($host)) ?: '/' : $url;
    }

    private static function absolute(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return null;
        }

        return str_starts_with($url, '/') && ! str_starts_with($url, '//') ? request()->getSchemeAndHttpHost().$url : $url;
    }
}
