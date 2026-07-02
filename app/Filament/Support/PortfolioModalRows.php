<?php

namespace App\Filament\Support;

use App\Models\CareerTimelineEntry;
use App\Models\Insight;
use App\Models\Poster;
use App\Models\PortfolioProject;
use App\Models\HomePage;
use App\Models\Profile;
use App\Models\Service;
use App\Models\Skill;

final class PortfolioModalRows
{
    public static function skill(Skill $record): array
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

    public static function service(Service $record): array
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

    public static function insight(Insight $record): array
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

    public static function poster(Poster $record): array
    {
        return [
            'title' => $record->title,
            'status' => ($record->is_published ?? false) ? 'Published on site' : 'Draft (hidden)',
            'rows' => [
                ['label' => 'Category', 'text' => $record->category],
                ['label' => 'Slug', 'text' => $record->slug],
                ['label' => 'Featured', 'text' => $record->is_featured ? 'Yes' : 'No'],
                ['label' => 'PDF', 'text' => $record->pdf ? basename($record->pdf) : '—'],
                ['label' => 'Short description', 'html' => $record->short_description ?: '—'],
            ],
        ];
    }

    public static function project(PortfolioProject $record): array
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

    public static function career(CareerTimelineEntry $record): array
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

    public static function profile(Profile $record): array
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

    public static function home(HomePage $record): array
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
