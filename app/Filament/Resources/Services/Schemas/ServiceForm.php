<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Schemas\Schema;

/**
 * @deprecated Use AdminServiceForm on create/edit pages.
 */
class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return AdminServiceForm::configure($schema);
    }
}
