<?php

namespace App\Models;

use App\Models\Concerns\HasPublishing;
use Illuminate\Database\Eloquent\Model;

class CareerTimelineEntry extends Model
{
    use HasPublishing;

    protected $fillable = [
        'sort_order',
        'period_label',
        'title',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
