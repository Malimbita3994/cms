<?php

namespace App\Models\Concerns;

trait HasPublishing
{
    public function scopePublished($query)
    {
        return $query->where($this->getTable().'.is_published', true);
    }

    public function initializeHasPublishing(): void
    {
        $this->mergeFillable(['is_published']);
        $this->mergeCasts(['is_published' => 'boolean']);
    }
}
