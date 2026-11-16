<?php

declare(strict_types=1);

namespace Atrium\Theme\Tests\Fixtures;

use Atrium\Admin\Fields\ContentField;
use Atrium\Admin\Fields\TextField;
use Atrium\Admin\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Translatable;

    protected $guarded = [];

    /** @var array<int,string> */
    protected array $translatable = ['name', 'code', 'detail_text'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public static function getAdminLabel(): string
    {
        return 'Posts';
    }

    public static function getAdministrationFields(): array
    {
        return [
            TextField::make('name')->label('Name'),
            ContentField::make('detail_text')->label('Text'),
        ];
    }
}
