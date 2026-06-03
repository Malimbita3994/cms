<?php

namespace App\Models;

use App\Models\Concerns\HasPublishing;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasPublishing;

    protected $fillable = [
        'sort_order',
        'name',
        'level',
        'focus',
        'icon',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'level' => 'integer',
        ];
    }
}
