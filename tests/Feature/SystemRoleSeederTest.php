<?php

namespace Tests\Feature;

use Database\Seeders\SystemPermissionSeeder;
use Database\Seeders\SystemRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SystemRoleSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_three_roles_with_expected_access(): void
    {
        $this->seed(SystemPermissionSeeder::class);
        $this->seed(SystemRoleSeeder::class);

        $superAdmin = Role::findByName('Super Admin', 'web');
        $contentManager = Role::findByName('Content manager', 'web');
        $auditor = Role::findByName('Auditor', 'web');

        $this->assertTrue($superAdmin->hasPermissionTo('manage-access-control'));
        $this->assertTrue($superAdmin->hasPermissionTo('create_user'));

        $this->assertFalse($contentManager->hasPermissionTo('manage-access-control'));
        $this->assertFalse($contentManager->hasPermissionTo('view_any_user'));
        $this->assertTrue($contentManager->hasPermissionTo('create_skill'));
        $this->assertTrue($contentManager->hasPermissionTo('delete_case_study'));

        $this->assertFalse($auditor->hasPermissionTo('manage-access-control'));
        $this->assertFalse($auditor->hasPermissionTo('create_skill'));
        $this->assertTrue($auditor->hasPermissionTo('view_any_skill'));
        $this->assertTrue($auditor->hasPermissionTo('view_dashboard'));
    }
}
