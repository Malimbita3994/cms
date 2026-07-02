<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PortfolioContentSeeder extends Seeder
{
    /** @var array<string, list<string>> */
    private const JSON_COLUMNS = [
        'profiles' => [
            'strengths',
            'about_strengths',
            'about_approach_steps',
            'about_values',
        ],
        'home_pages' => ['core_focus_strengths'],
        'portfolio_projects' => ['technologies', 'achievements'],
        'case_studies' => ['stack'],
        'discipline_pages' => ['items'],
    ];

    public function run(): void
    {
        $tables = [
            'site_settings',
            'profiles',
            'home_pages',
            'career_timeline_entries',
            'skills',
            'services',
            'portfolio_projects',
            'insights',
            'case_studies',
            'discipline_pages',
            'posters',
        ];

        Schema::disableForeignKeyConstraints();

        foreach ($tables as $table) {
            $path = database_path("seeders/data/{$table}.json");

            if (! file_exists($path)) {
                $this->command?->warn("Skipped {$table}: JSON file not found.");

                continue;
            }

            $rows = json_decode(file_get_contents($path), true);

            if (! is_array($rows)) {
                $this->command?->warn("Skipped {$table}: invalid JSON.");

                continue;
            }

            DB::table($table)->truncate();

            if ($rows === []) {
                $this->command?->info("Seeded {$table}: 0 rows.");

                continue;
            }

            $now = now()->toDateTimeString();
            $encoded = array_map(function (array $row) use ($table, $now): array {
                $row['created_at'] ??= $now;
                $row['updated_at'] ??= $now;

                foreach (self::JSON_COLUMNS[$table] ?? [] as $column) {
                    if (array_key_exists($column, $row) && is_array($row[$column])) {
                        $row[$column] = json_encode($row[$column], JSON_UNESCAPED_UNICODE);
                    }
                }

                return $row;
            }, $rows);

            foreach (array_chunk($encoded, 100) as $chunk) {
                DB::table($table)->insert($chunk);
            }

            $this->command?->info('Seeded '.$table.': '.count($rows).' rows.');
        }

        Schema::enableForeignKeyConstraints();
    }
}
