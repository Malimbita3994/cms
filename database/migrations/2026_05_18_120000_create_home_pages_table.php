<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_pages', function (Blueprint $table) {
            $table->id();
            $table->string('availability_prefix')->default('Available for projects');
            $table->string('hero_background_image')->default('/Home.jpg?v=2');
            $table->string('hero_profile_image')->nullable();
            $table->string('primary_cta_label')->default('Start a Project');
            $table->string('primary_cta_url')->default('/contact');
            $table->string('secondary_cta_label')->default('View my Works');
            $table->string('secondary_cta_url')->default('#projects');
            $table->string('cv_cta_label')->default('View CV');
            $table->string('projects_stat_title')->default('Projects');
            $table->string('projects_stat_subtitle')->default('Delivered systems');
            $table->string('services_stat_title')->default('Services');
            $table->string('services_stat_subtitle')->default('Core offerings');
            $table->string('insights_stat_title')->default('Insights');
            $table->string('insights_stat_subtitle')->default('Published notes');
            $table->string('core_focus_title')->default('Core Focus');
            $table->text('core_focus_blurb')->nullable();
            $table->boolean('show_core_focus')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_pages');
    }
};
