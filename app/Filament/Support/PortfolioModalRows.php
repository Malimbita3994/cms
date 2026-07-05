<?php

namespace App\Filament\Support;

use App\Models\Poster;
use App\Support\PortfolioAsset;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;

final class PortfolioModalRows
{
    public static function skill(\App\Models\Skill $record): array
    {
        return [
            'title' => $record->name,
            'status' => ($record->is_published ?? true) ? 'Published on site' : 'Draft (hidden)',
            'rows' => [
                ['label' => 'Proficiency', 'text' => $record->level.'%'],
                ['label' => 'Focus', 'html' => $record->focus],
            ],
        ];
    }

    public static function service(\App\Models\Service $record): array
    {
        return [
            'title' => $record->title,
            'status' => ($record->is_published ?? true) ? 'Published on site' : 'Draft (hidden)',
            'rows' => [
                ['label' => 'Tagline', 'text' => $record->tagline ?: '—'],
                ['label' => 'Description', 'html' => $record->description],
            ],
        ];
    }

    public static function insight(\App\Models\Insight $record): array
    {
        return [
            'title' => $record->title,
            'status' => ($record->is_published ?? true) ? 'Published on site' : 'Draft (hidden)',
            'rows' => [
                ['label' => 'Date', 'text' => $record->display_date ?: '—'],
                ['label' => 'Excerpt', 'html' => $record->excerpt],
            ],
        ];
    }

    /**
     * @return array{
     *     title: string,
     *     typeLabel: string,
     *     isPublished: bool,
     *     category: string,
     *     categorySlug: string,
     *     slug: string,
     *     isFeatured: bool,
     *     imageUrl: string|null,
     *     pdf: array{name: string, url: string, size: string|null}|null,
     *     shortDescription: string|null,
     *     publishedAt: \Illuminate\Support\Carbon|null,
     *     updatedAt: \Illuminate\Support\Carbon|null,
     *     id: int
     * }
     */
    public static function poster(Poster $record): array
    {
        $imageRelative = PortfolioAsset::toUploadState($record->image);
        $imageUrl = $imageRelative !== null
            ? url('/media/'.ltrim($imageRelative, '/'))
            : null;

        $pdfRelative = PortfolioAsset::toUploadState($record->pdf);
        $pdf = null;

        if ($pdfRelative !== null) {
            $disk = Storage::disk(PortfolioAsset::DISK);
            $pdf = [
                'name' => basename($record->pdf),
                'url' => url('/media/'.ltrim($pdfRelative, '/')),
                'size' => $disk->exists($pdfRelative)
                    ? Number::fileSize($disk->size($pdfRelative), precision: 1)
                    : null,
            ];
        }

        return [
            'title' => $record->title,
            'typeLabel' => $record->category.' post',
            'isPublished' => (bool) $record->is_published,
            'category' => $record->category,
            'categorySlug' => PosterCategoryColors::slug($record->category),
            'slug' => $record->slug,
            'isFeatured' => (bool) $record->is_featured,
            'imageUrl' => $imageUrl,
            'pdf' => $pdf,
            'shortDescription' => $record->short_description,
            'publishedAt' => $record->published_at,
            'updatedAt' => $record->updated_at,
            'id' => $record->id,
        ];
    }

    public static function project(\App\Models\PortfolioProject $record): array
    {
        return [
            'title' => $record->title,
            'status' => ($record->is_published ?? true) ? 'Published on site' : 'Draft (hidden)',
            'rows' => [
                ['label' => 'Preview', 'text' => $record->preview ?: '—'],
                ['label' => 'Description', 'html' => $record->description],
            ],
        ];
    }

    public static function career(\App\Models\CareerTimelineEntry $record): array
    {
        return [
            'title' => $record->title,
            'status' => ($record->is_published ?? true) ? 'Published on site' : 'Draft (hidden)',
            'rows' => [
                ['label' => 'Period', 'text' => $record->period_label],
                ['label' => 'Summary', 'html' => $record->description],
            ],
        ];
    }

    public static function profile(\App\Models\Profile $record): array
    {
        return [
            'title' => $record->name,
            'status' => ($record->is_published ?? true) ? 'Published on site' : 'Draft (hidden)',
            'rows' => [
                ['label' => 'Role', 'text' => $record->role],
                ['label' => 'Email', 'text' => $record->email],
                ['label' => 'Summary', 'html' => $record->summary],
            ],
        ];
    }

    public static function home(\App\Models\HomePage $record): array
    {
        return [
            'title' => $record->headline ?: 'Home page',
            'status' => ($record->is_published ?? true) ? 'Published on site' : 'Draft (hidden)',
            'rows' => [
                ['label' => 'Headline', 'text' => $record->headline ?: '—'],
                ['label' => 'Hero summary', 'html' => $record->hero_summary],
                ['label' => 'Core focus', 'text' => $record->core_focus_title ?: '—'],
            ],
        ];
    }
}
