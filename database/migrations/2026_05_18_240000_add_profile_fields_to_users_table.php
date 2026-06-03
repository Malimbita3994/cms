<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('email');
            $table->text('bio')->nullable()->after('avatar');
            $table->timestamp('last_login_at')->nullable()->after('bio');
        });

        User::query()->each(function (User $user): void {
            if (filled($user->username)) {
                return;
            }

            $base = Str::slug(Str::before($user->email, '@'), '_');

            if ($base === '') {
                $base = 'user_'.$user->id;
            }

            $username = $base;
            $suffix = 1;

            while (User::query()->where('username', $username)->whereKeyNot($user->id)->exists()) {
                $username = $base.'_'.$suffix;
                $suffix++;
            }

            $user->forceFill(['username' => $username])->saveQuietly();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'bio', 'last_login_at']);
        });
    }
};
