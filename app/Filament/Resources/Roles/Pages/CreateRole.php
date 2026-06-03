<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Concerns\SyncsRolePermissions;
use App\Filament\Resources\Roles\RoleResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateRole extends CreateRecord
{
    use InteractsWithPortfolioEditor;
    use SyncsRolePermissions;

    protected static string $resource = RoleResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->pullPermissionsFromFormData($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $role = static::getModel()::query()->create($data);
        $this->record = $role;
        $this->syncPermissionsToRole();

        return $role;
    }

    protected function beforeCreate(): void
    {
        $this->swalLoading('Creating role…');
    }

    protected function afterCreate(): void
    {
        $name = (string) $this->record->name;

        session()->flash('swal_flash', [
            'type' => 'success',
            'title' => 'Role created',
            'text' => $name !== ''
                ? "“{$name}” has been added with the selected permissions."
                : 'The role has been added with the selected permissions.',
        ]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function getRedirectUrl(): string
    {
        return RoleResource::getUrl('index');
    }
}
