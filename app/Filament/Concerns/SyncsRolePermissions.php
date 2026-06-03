<?php

namespace App\Filament\Concerns;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

trait SyncsRolePermissions
{
    /** @var array<int, int|string> */
    protected array $rolePermissionIds = [];

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function pullPermissionsFromFormData(array $data): array
    {
        $this->rolePermissionIds = array_values(array_filter((array) ($data['permissions'] ?? [])));
        unset($data['permissions']);

        return $data;
    }

    protected function syncPermissionsToRole(): void
    {
        if ($this->record->name === 'Super Admin') {
            $this->record->syncPermissions(
                Permission::query()->where('guard_name', 'web')->pluck('name')->all(),
            );
        } else {
            $names = Permission::query()
                ->where('guard_name', 'web')
                ->whereIn('id', $this->rolePermissionIds)
                ->pluck('name')
                ->all();

            $this->record->syncPermissions($names);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
