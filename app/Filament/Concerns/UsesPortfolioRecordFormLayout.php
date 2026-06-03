<?php

namespace App\Filament\Concerns;

/**
 * Full-width portfolio editor shell (TipTap fields, custom header/footer).
 */
trait UsesPortfolioRecordFormLayout
{
    public function getView(): string
    {
        return 'filament.pages.portfolio-record-form';
    }

    public function hasResourceBreadcrumbs(): bool
    {
        return false;
    }
}
