<?php

namespace Database\Seeders;

use Filament\Pages\Page;
use Filament\Resources\Resource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class SystemPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = collect()
            ->merge($this->discoverResourcePermissions())
            ->merge($this->discoverPagePermissions())
            ->push('manage-access-control')
            ->unique()
            ->values();

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }

    /**
     * @return array<int, string>
     */
    protected function discoverResourcePermissions(): array
    {
        $resourceFiles = File::glob(app_path('Filament/Resources/*/*Resource.php'));
        $permissions = [];

        foreach ($resourceFiles as $resourceFile) {
            $class = 'App\\'.str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($resourceFile, app_path().DIRECTORY_SEPARATOR)
            );

            if (! class_exists($class) || ! is_subclass_of($class, Resource::class)) {
                continue;
            }

            $model = $class::getModel();

            if (! is_string($model) || ! class_exists($model)) {
                continue;
            }

            $subject = Str::snake(class_basename($model));
            $permissions = [
                ...$permissions,
                "view_any_{$subject}",
                "view_{$subject}",
                "create_{$subject}",
                "update_{$subject}",
                "delete_{$subject}",
                "delete_any_{$subject}",
            ];
        }

        return $permissions;
    }

    /**
     * @return array<int, string>
     */
    protected function discoverPagePermissions(): array
    {
        $pageFiles = File::glob(app_path('Filament/Pages/*.php'));
        $permissions = [];

        foreach ($pageFiles as $pageFile) {
            $class = 'App\\'.str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($pageFile, app_path().DIRECTORY_SEPARATOR)
            );

            if (! class_exists($class) || ! is_subclass_of($class, Page::class)) {
                continue;
            }

            $permissions[] = 'view_'.Str::snake(class_basename($class));
        }

        return $permissions;
    }
}
