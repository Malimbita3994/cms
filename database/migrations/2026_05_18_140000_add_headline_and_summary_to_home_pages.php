<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_pages', function (Blueprint $table) {
            $table->string('headline')->nullable()->after('availability_prefix');
            $table->text('hero_summary')->nullable()->after('headline');
            $table->json('core_focus_strengths')->nullable()->after('core_focus_blurb');
        });
    }

    public function down(): void
    {
        Schema::table('home_pages', function (Blueprint $table) {
            $table->dropColumn(['headline', 'hero_summary', 'core_focus_strengths']);
        });
    }
};
