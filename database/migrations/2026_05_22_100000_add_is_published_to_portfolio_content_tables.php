<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var list<string> */
    private const TABLES = [
        'skills',
        'services',
        'insights',
        'portfolio_projects',
        'career_timeline_entries',
        'profiles',
        'home_pages',
    ];

    public function up(): void
    {
        foreach (self::TABLES as $tableName) {
            if (! Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'is_published')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->boolean('is_published')->default(true);
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $tableName) {
            if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, 'is_published')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('is_published');
            });
        }
    }
};
