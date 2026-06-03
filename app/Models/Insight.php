<?php

namespace App\Models;

use App\Models\Concerns\HasPublishing;
use Illuminate\Database\Eloquent\Model;

class Insight extends Model
{
    use HasPublishing;

    protected $fillable = [
        'sort_order',
        'title',
        'excerpt',
        'display_date',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
