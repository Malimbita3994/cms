<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::query()->where('email', 'admin@example.local')->first();

if (! $user) {
    echo "User admin@example.local not found.\n";
    exit(1);
}

$user->update(['password' => 'password']);
$user->assignRole('Super Admin');

echo "Login: admin@example.local / password\n";
