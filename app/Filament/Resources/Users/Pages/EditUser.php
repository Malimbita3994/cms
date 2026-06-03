<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Resources\Users\Schemas\AdminUserForm;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Support\PortfolioEditorActions;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class EditUser extends EditRecord
{
    use HasPortfolioRecordShell;
    use InteractsWithPortfolioEditor;

    protected static string $resource = UserResource::class;

    protected string $view = 'filament.pages.portfolio-record-form';

    public function getPortfolioFormTitle(): string
    {
        return 'Edit user';
    }

    public function getPortfolioFormLead(): string
    {
        return 'Update account details, roles, and password for '.$this->getRecord()->name.'.';
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Users';
    }

    public function form(Schema $schema): Schema
    {
        return AdminUserForm::configure($schema, creating: false);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['roles'] = $this->getRecord()->roles->pluck('name')->all();

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $roles = array_values(array_filter((array) ($data['roles'] ?? [])));
        $payload = Arr::only($data, ['name', 'email', 'is_active']);

        if ($record->id === auth()->id()) {
            $payload['is_active'] = true;
        }

        if (filled($data['password'] ?? null)) {
            $payload['password'] = $data['password'];
        }

        $record->update($payload);
        $record->syncRoles($roles);

        return $record;
    }

    public function cancel(): void
    {
        $this->redirect(UserResource::getUrl('index'));
    }

    protected function beforeSave(): void
    {
        $this->swalLoading('Saving user…');
    }

    protected function afterSave(): void
    {
        $name = (string) $this->record->name;

        session()->flash('swal_flash', [
            'type' => 'success',
            'title' => 'User updated',
            'text' => $name !== ''
                ? "“{$name}” has been saved."
                : 'Account details have been saved.',
        ]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function getRedirectUrl(): string
    {
        return UserResource::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn (): bool => $this->getRecord()->id === auth()->id())
                ->requiresConfirmation(false)
                ->extraAttributes(PortfolioEditorActions::sweetConfirmAttributes(
                    'Delete this user?',
                    'This account will be removed permanently.',
                ))
                ->successNotification(null)
                ->after(fn (): mixed => $this->dispatch(
                    'swal',
                    type: 'success',
                    title: 'User deleted',
                    text: 'The account has been removed.',
                )),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    public function getHeader(): ?View
    {
        return null;
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return null;
    }
}
