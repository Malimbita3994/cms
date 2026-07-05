<?php

namespace App\Filament\Support;

final class PosterCategoryColors
{
    /** @var array<string, string> */
    private const SLUGS = [
        'News' => 'news',
        'Updates' => 'updates',
        'Entertainment' => 'entertainment',
        'Blog' => 'blog',
        'Announcements' => 'announcements',
        'General' => 'general',
    ];

    public static function slug(string $category): string
    {
        return self::SLUGS[$category] ?? 'general';
    }

    public static function badgeHtml(string $category): string
    {
        $slug = self::slug($category);

        return sprintf(
            '<span class="poster-category-badge poster-category-badge--%s">%s</span>',
            e($slug),
            e($category),
        );
    }
}
