<?php

namespace App\Models;

use App\Models\Concerns\HasPublishing;
use Illuminate\Database\Eloquent\Model;

class DisciplinePage extends Model
{
    use HasPublishing;

    public const SLUG_DUTIES = 'business-analysis-duties';

    public const SLUG_RESPONSIBILITY = 'professional-responsibility';

    protected $fillable = [
        'slug',
        'title',
        'hero_eyebrow',
        'hero_title',
        'hero_description',
        'items',
        'body',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public static function duties(): ?self
    {
        return static::query()->where('slug', self::SLUG_DUTIES)->first();
    }

    public static function professionalResponsibility(): ?self
    {
        return static::query()->where('slug', self::SLUG_RESPONSIBILITY)->first();
    }
}
