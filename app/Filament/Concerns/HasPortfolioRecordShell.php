<?php

namespace App\Filament\Concerns;

use Illuminate\Contracts\Support\Htmlable;

trait HasPortfolioRecordShell
{
    abstract public function getPortfolioFormTitle(): string;

    abstract public function getPortfolioFormLead(): string;

    abstract public function getPortfolioFormBreadcrumb(): string;

    public function getTitle(): string|Htmlable
    {
        return $this->getPortfolioFormTitle();
    }
}
