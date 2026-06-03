<?php

use App\Models\Service;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('tagline', 255)->nullable()->after('title');
        });

        Service::query()->whereNull('tagline')->each(function (Service $service): void {
            $plain = trim(strip_tags((string) $service->description));
            if ($plain === '') {
                return;
            }

            $service->update([
                'tagline' => Str::limit($plain, 120, '…'),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('tagline');
        });
    }
};
