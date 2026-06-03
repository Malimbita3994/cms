<?php

namespace App\Models;

use App\Models\Concerns\HasPublishing;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasPublishing;

    protected $fillable = [
        'sort_order',
        'title',
        'tagline',
        'image',
        'description',
        'icon',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
