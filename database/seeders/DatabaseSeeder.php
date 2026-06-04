<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(SystemPermissionSeeder::class);
        $this->call(SystemRoleSeeder::class);

        $admin = User::factory()->create([
            'name' => 'CMS Admin',
            'email' => 'admin@example.local',
            'password' => 'password',
        ]);

        $admin->assignRole('Super Admin');

        $this->call(PortfolioContentSeeder::class);
    }
}
