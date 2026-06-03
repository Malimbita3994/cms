<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseStudy extends Model
{
    protected $fillable = [
        'sort_order',
        'is_published',
        'title',
        'image',
        'desc',
        'impact',
        'stack',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_published' => 'boolean',
            'stack' => 'array',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
