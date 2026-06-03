<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SystemRoleSeeder extends Seeder
{
    /** @var list<string> */
    private const ACCESS_CONTROL_SUBJECTS = ['user', 'role', 'permission'];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->pluck('name');

        $this->syncRole(
            'Super Admin',
            $permissions->all(),
        );

        $this->syncRole(
            'Content manager',
            $permissions
                ->reject(fn (string $name): bool => $this->isAccessControlPermission($name))
                ->values()
                ->all(),
        );

        $this->syncRole(
            'Auditor',
            $permissions
                ->filter(fn (string $name): bool => $this->isAuditorPermission($name))
                ->values()
                ->all(),
        );
    }

    /**
     * @param  list<string>  $permissionNames
     */
    private function syncRole(string $roleName, array $permissionNames): void
    {
        $role = Role::query()->firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($permissionNames);
    }

    private function isAccessControlPermission(string $permission): bool
    {
        if ($permission === 'manage-access-control') {
            return true;
        }

        $subject = $this->permissionSubject($permission);

        return $subject !== null && in_array($subject, self::ACCESS_CONTROL_SUBJECTS, true);
    }

    private function isAuditorPermission(string $permission): bool
    {
        if ($this->isAccessControlPermission($permission)) {
            return false;
        }

        if (in_array($permission, ['view_any', 'create', 'update', 'delete', 'delete_any'], true)) {
            return false;
        }

        return str_starts_with($permission, 'view_any_')
            || str_starts_with($permission, 'view_');
    }

    private function permissionSubject(string $permission): ?string
    {
        foreach (['delete_any', 'view_any', 'delete', 'create', 'update', 'view'] as $prefix) {
            if (str_starts_with($permission, "{$prefix}_")) {
                return Str::after($permission, "{$prefix}_");
            }
        }

        return null;
    }
}
