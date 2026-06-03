<?php

namespace App\Filament\Concerns;

/**
 * Custom portfolio forms call $wire.save() from JavaScript; Filament create pages only expose create().
 */
trait DelegatesSaveToCreate
{
    public function save(): void
    {
        $this->create();
    }
}
