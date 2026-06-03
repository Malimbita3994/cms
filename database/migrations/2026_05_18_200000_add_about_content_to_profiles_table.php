<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('about_eyebrow')->nullable()->after('image');
            $table->string('about_heading_lead')->nullable()->after('about_eyebrow');
            $table->string('about_heading_accent')->nullable()->after('about_heading_lead');
            $table->json('about_strengths')->nullable()->after('about_heading_accent');
            $table->json('about_approach_steps')->nullable()->after('about_strengths');
            $table->json('about_values')->nullable()->after('about_approach_steps');
            $table->string('about_page_hero_eyebrow')->nullable()->after('about_values');
            $table->string('about_page_hero_title')->nullable()->after('about_page_hero_eyebrow');
            $table->text('about_page_hero_description')->nullable()->after('about_page_hero_title');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn([
                'about_eyebrow',
                'about_heading_lead',
                'about_heading_accent',
                'about_strengths',
                'about_approach_steps',
                'about_values',
                'about_page_hero_eyebrow',
                'about_page_hero_title',
                'about_page_hero_description',
            ]);
        });
    }
};
