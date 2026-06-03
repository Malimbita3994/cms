<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Concerns\SyncsRolePermissions;
use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditRole extends EditRecord
{
    use InteractsWithPortfolioEditor;
    use SyncsRolePermissions;

    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['permissions'] = $this->record->permissions()->pluck('id')->all();

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->pullPermissionsFromFormData($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        $this->syncPermissionsToRole();

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn (): bool => $this->record->name === 'Super Admin'),
        ];
    }

    protected function beforeSave(): void
    {
        $this->swalLoading('Saving role…');
    }

    protected function afterSave(): void
    {
        $name = (string) $this->record->name;

        session()->flash('swal_flash', [
            'type' => 'success',
            'title' => 'Role saved',
            'text' => $name !== ''
                ? "“{$name}” and its permissions have been updated."
                : 'The role and its permissions have been updated.',
        ]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function getRedirectUrl(): string
    {
        return RoleResource::getUrl('index');
    }
}
