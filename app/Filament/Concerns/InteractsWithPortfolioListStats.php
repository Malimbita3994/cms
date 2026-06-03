<?php

namespace App\Filament\Concerns;

trait InteractsWithPortfolioListStats
{
    /** @var array<int, array{label: string, value: int|string, hint: string, color: string}> */
    public array $listStats = [];

    public function loadListStats(): void
    {
        $this->listStats = $this->computeListStats();
    }

    /**
     * @return array<int, array{label: string, value: int|string, hint: string, color: string}>
     */
    abstract protected function computeListStats(): array;
}
