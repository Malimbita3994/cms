<?php

namespace Database\Seeders;

use App\Models\Poster;
use App\Models\User;
use App\Support\SiteContentCache;
use Illuminate\Database\Seeder;

class PosterSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/posters.json');

        if (! file_exists($path)) {
            $this->command?->warn('Skipped posters: JSON file not found.');

            return;
        }

        $rows = json_decode(file_get_contents($path), true);

        if (! is_array($rows)) {
            $this->command?->warn('Skipped posters: invalid JSON.');

            return;
        }

        $userId = User::query()->value('id');
        $now = now();

        foreach ($rows as $row) {
            if (! is_array($row) || empty($row['slug'])) {
                continue;
            }

            Poster::query()->updateOrCreate(
                ['slug' => (string) $row['slug']],
                [
                    'sort_order' => (int) ($row['sort_order'] ?? 0),
                    'is_published' => (bool) ($row['is_published'] ?? true),
                    'is_featured' => (bool) ($row['is_featured'] ?? false),
                    'title' => (string) $row['title'],
                    'category' => (string) ($row['category'] ?? 'General'),
                    'short_description' => (string) ($row['short_description'] ?? ''),
                    'content' => (string) ($row['content'] ?? ''),
                    'image' => $row['image'] ?? null,
                    'published_at' => $row['published_at'] ?? $now,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ],
            );
        }

        SiteContentCache::flush();

        $this->command?->info('Seeded posters: '.count($rows).' rows.');
    }
}
