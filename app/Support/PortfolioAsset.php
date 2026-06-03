<?php

namespace App\Support;

use Filament\Forms\Components\BaseFileUpload;
use Illuminate\Support\Facades\Storage;

final class PortfolioAsset
{
    public const DISK = 'portfolio';

    public static function diskRoot(): string
    {
        return (string) config('filesystems.disks.portfolio.root');
    }

    /**
     * Form state for FileUpload (path relative to portfolio disk).
     */
    public static function toUploadState(?string $publicPath): ?string
    {
        if ($publicPath === null || $publicPath === '') {
            return null;
        }

        $pathOnly = parse_url($publicPath, PHP_URL_PATH) ?? $publicPath;
        $relative = ltrim((string) $pathOnly, '/');

        if ($relative === '' || ! Storage::disk(self::DISK)->exists($relative)) {
            return null;
        }

        return $relative;
    }

    /**
     * Public URL path for the Next.js site (e.g. /uploads/home/hero.jpg).
     */
    public static function toPublicPath(mixed $uploaded): ?string
    {
        if ($uploaded === null || $uploaded === '') {
            return null;
        }

        if (is_array($uploaded)) {
            $uploaded = $uploaded[array_key_first($uploaded)] ?? null;
        }

        if (! is_string($uploaded) || $uploaded === '') {
            return null;
        }

        if (str_starts_with($uploaded, '/')) {
            return $uploaded;
        }

        return '/'.ltrim($uploaded, '/');
    }

    /**
     * Filament FileUpload preview resolver (admin /media URLs).
     */
    public static function uploadedFileResolver(
        BaseFileUpload $component,
        string $file,
        string|array|null $storedFileNames,
    ): ?array {
        $storage = Storage::disk(self::DISK);

        if (! $storage->exists($file)) {
            return null;
        }

        return [
            'name' => ($component->isMultiple() ? ($storedFileNames[$file] ?? null) : $storedFileNames) ?? basename($file),
            'size' => $storage->size($file),
            'type' => $storage->mimeType($file),
            'url' => url('/media/'.ltrim($file, '/')),
        ];
    }

    public static function previewUrl(?string $publicPath): ?string
    {
        if ($publicPath === null || $publicPath === '') {
            return null;
        }

        if (str_starts_with($publicPath, 'http://') || str_starts_with($publicPath, 'https://')) {
            return $publicPath;
        }

        $relative = ltrim($publicPath, '/');

        if (Storage::disk(self::DISK)->exists($relative)) {
            $nextBase = rtrim((string) config('services.portfolio.asset_base_url'), '/');

            return $nextBase !== '' ? $nextBase.'/'.ltrim($publicPath, '/') : '/'.ltrim($publicPath, '/');
        }

        return null;
    }
}
