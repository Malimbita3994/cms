<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Concerns\DelegatesSaveToCreate;
use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Resources\Users\Schemas\AdminUserForm;
use App\Filament\Resources\Users\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    use DelegatesSaveToCreate;
    use HasPortfolioRecordShell;
    use InteractsWithPortfolioEditor;

    protected static string $resource = UserResource::class;

    protected string $view = 'filament.pages.portfolio-record-form';

    public function getPortfolioFormTitle(): string
    {
        return 'New user';
    }

    public function getPortfolioFormLead(): string
    {
        return 'Create a new admin account and assign roles.';
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Users';
    }

    public function form(Schema $schema): Schema
    {
        return AdminUserForm::configure($schema, creating: true);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $roles = array_values(array_filter((array) ($data['roles'] ?? [])));

        $user = static::getModel()::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        $user->syncRoles($roles);

        return $user;
    }

    public function cancel(): void
    {
        $this->redirect(UserResource::getUrl('index'));
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

    protected function beforeCreate(): void
    {
        $this->swalLoading('Creating user…');
    }

    protected function afterCreate(): void
    {
        $this->swalSuccess('User created', 'The new account has been added.');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function getRedirectUrl(): string
    {
        return UserResource::getUrl('index');
    }
}
