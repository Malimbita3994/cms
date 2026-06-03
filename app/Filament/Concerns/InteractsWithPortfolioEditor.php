<?php

namespace App\Filament\Concerns;

use App\Filament\Support\PortfolioFormFields;
use App\Support\DashboardMetrics;
use App\Support\SiteContentCache;
use Illuminate\Support\Facades\Http;

trait InteractsWithPortfolioEditor
{
    /** @return array<int, array<int, string|\Filament\Forms\Components\RichEditor\ToolbarButtonGroup>> */
    protected function richToolbarButtons(): array
    {
        return PortfolioFormFields::richToolbarButtons();
    }

    protected function portfolioImageUpload(
        string $name,
        string $directory,
        string $label,
        int $previewHeight,
        string $helperText,
        bool $required = true,
    ): \Filament\Forms\Components\FileUpload {
        return PortfolioFormFields::imageUpload($name, $directory, $label, $previewHeight, $helperText, $required);
    }

    protected function swalLoading(string $title = 'Saving...', ?string $text = null): void
    {
        $this->dispatch('swal-loading', title: $title, text: $text);
    }

    protected function swalSuccess(string $title, ?string $text = null): void
    {
        $this->dispatch('swal', type: 'success', title: $title, text: $text);
    }

    protected function swalError(string $title, ?string $text = null): void
    {
        $this->dispatch('swal', type: 'error', title: $title, text: $text);
    }

    /**
     * Refresh the Next.js site after the HTTP response is sent (avoids blocking save).
     */
    protected function queueSiteRevalidation(): void
    {
        $baseUrl = config('services.next.url');
        $secret = config('services.next.revalidate_secret');

        if (! filled($baseUrl) || ! filled($secret)) {
            return;
        }

        $url = rtrim((string) $baseUrl, '/').'/api/revalidate';

        defer(function () use ($url, $secret): void {
            try {
                Http::timeout(2)
                    ->connectTimeout(1)
                    ->withHeaders(['x-revalidate-secret' => $secret])
                    ->post($url);
            } catch (\Throwable) {
                // Non-blocking: CMS save still succeeds if Next is offline.
            }

            DashboardMetrics::flush();
            SiteContentCache::flush();
        });
    }

    /** @deprecated Use queueSiteRevalidation() */
    protected function revalidatePublicSite(): void
    {
        $this->queueSiteRevalidation();
    }
}
