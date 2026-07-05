<?php

namespace Tests\Feature;

use App\Filament\Resources\Skills\SkillResource;
use App\Models\User;
use Database\Seeders\SystemPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionEnforcementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SystemPermissionSeeder::class);
    }

    public function test_role_syncs_selected_permissions(): void
    {
        $role = Role::query()->create([
            'name' => 'Editor',
            'guard_name' => 'web',
        ]);

        $permissionIds = Permission::query()
            ->whereIn('name', ['view_any_skill', 'view_skill'])
            ->pluck('id')
            ->all();

        $role->syncPermissions(
            Permission::query()->whereIn('id', $permissionIds)->pluck('name')->all()
        );

        $role->refresh();

        $this->assertTrue($role->hasPermissionTo('view_any_skill'));
        $this->assertTrue($role->hasPermissionTo('view_skill'));
        $this->assertFalse($role->hasPermissionTo('create_user'));
    }

    public function test_user_without_any_permissions_cannot_access_panel_home(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->assertFalse(\App\Support\FilamentPermissions::hasAnyCmsPermission($user));
        $this->assertFalse(\App\Filament\Pages\Dashboard::canAccess());

        $this->get('/admin')
            ->assertRedirect('/');
    }

    public function test_user_without_permission_cannot_access_skill_resource(): void
    {
        $role = Role::query()->create([
            'name' => 'No Skills',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user);

        $this->assertFalse(SkillResource::canViewAny());
        $this->assertFalse(SkillResource::canAccess());
    }

    public function test_user_with_skill_permissions_can_access_skill_resource(): void
    {
        $role = Role::query()->create([
            'name' => 'Skill Editor',
            'guard_name' => 'web',
        ]);
        $role->givePermissionTo(['view_any_skill', 'view_skill', 'create_skill', 'update_skill']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user);

        $this->assertTrue(SkillResource::canViewAny());
        $this->assertTrue(SkillResource::canAccess());
        $this->assertTrue(SkillResource::canCreate());
        $this->assertFalse(SkillResource::canDeleteAny());
    }
}
