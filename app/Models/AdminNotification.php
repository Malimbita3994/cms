<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AdminNotification extends Model
{
    protected $fillable = [
        'type',
        'category',
        'title',
        'body',
        'url',
        'icon',
        'subject_type',
        'subject_id',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function readers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'admin_notification_reads')
            ->withPivot('read_at');
    }
}
