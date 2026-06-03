<?php

namespace App\Filament\Concerns;

use Filament\Schemas\Schema;

/**
 * Single-column Filament form layout (image and rich text stacked vertically).
 */
trait ConfiguresStackedPortfolioForm
{
    public function defaultForm(Schema $schema): Schema
    {
        if (! $schema->hasCustomColumns()) {
            $schema->columns(1);
        }

        return parent::defaultForm($schema)->columns(1);
    }
}
