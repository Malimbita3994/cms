<?php

namespace App\Filament\Concerns;

trait HasPortfolioRecordViewUrls
{
    abstract public function getPortfolioEditUrl(): string;

    public function getPortfolioIndexUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
