<?php

namespace App\Models;

use App\Models\Concerns\HasPublishing;
use Illuminate\Database\Eloquent\Model;

class PortfolioProject extends Model
{
    use HasPublishing;

    protected $fillable = [
        'sort_order',
        'title',
        'description',
        'technologies',
        'achievements',
        'image',
        'preview',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'technologies' => 'array',
            'achievements' => 'array',
        ];
    }
}
