<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discipline_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('hero_eyebrow')->default('Discipline');
            $table->string('hero_title');
            $table->text('hero_description')->nullable();
            /** Numbered duty lines (business-analysis-duties) or null */
            $table->json('items')->nullable();
            /** Rich HTML body (professional-responsibility) or null */
            $table->longText('body')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discipline_pages');
    }
};
