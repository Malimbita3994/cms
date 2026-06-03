<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('app_name');
            $table->string('site_title');
            $table->text('site_description');
            $table->string('site_url')->default('https://example.com');
            $table->timestamps();
        });

        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->text('tagline');
            $table->text('summary');
            $table->json('strengths');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('location')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('image')->nullable()->comment('Public path e.g. /profile-hd.png?v=7');
            $table->timestamps();
        });

        Schema::create('career_timeline_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('period_label');
            $table->string('title');
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('name');
            $table->unsignedTinyInteger('level');
            $table->text('focus');
            $table->string('icon');
            $table->timestamps();
        });

        Schema::create('portfolio_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('title');
            $table->text('description');
            $table->json('technologies');
            $table->json('achievements');
            $table->string('image');
            $table->string('preview');
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('title');
            $table->text('description');
            $table->string('icon');
            $table->timestamps();
        });

        Schema::create('insights', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('title');
            $table->text('excerpt');
            $table->string('display_date')->nullable()->comment('e.g. March 2026');
            $table->string('image');
            $table->timestamps();
        });

        Schema::create('case_studies', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('title');
            $table->text('desc');
            $table->text('impact');
            $table->json('stack');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_studies');
        Schema::dropIfExists('insights');
        Schema::dropIfExists('services');
        Schema::dropIfExists('portfolio_projects');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('career_timeline_entries');
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('site_settings');
    }
};
