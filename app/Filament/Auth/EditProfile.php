<?php

namespace App\Filament\Auth;

use App\Models\User;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EditProfile extends BaseEditProfile
{
    protected string $view = 'filament.auth.edit-profile';

    public string $activeTab = 'profile';

    public function mount(): void
    {
        $this->getUser()->load('roles');

        parent::mount();
    }

    public function getHeading(): string | Htmlable | null
    {
        return null;
    }

    public function getSubheading(): string | Htmlable | null
    {
        return null;
    }

    public function setActiveTab(string $tab): void
    {
        if (! in_array($tab, ['profile', 'security', 'preferences', 'activity'], true)) {
            return;
        }

        $this->activeTab = $tab;
    }

    public function resetProfileForm(): void
    {
        $this->fillForm();
    }

    public function downloadMyData(): StreamedResponse
    {
        $user = $this->getUser();

        $payload = [
            'exported_at' => now()->toIso8601String(),
            'user' => $user->only([
                'id',
                'name',
                'email',
                'username',
                'bio',
                'is_active',
                'email_verified_at',
                'last_login_at',
                'created_at',
                'updated_at',
            ]),
            'roles' => $user->roles->pluck('name')->values()->all(),
        ];

        $filename = 'profile-'.($user->username ?? 'export').'-'.now()->format('Y-m-d').'.json';

        return response()->streamDownload(
            static function () use ($payload): void {
                echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            },
            $filename,
            ['Content-Type' => 'application/json'],
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Grid::make()
                        ->columns(['default' => 1, 'xl' => 3])
                        ->schema([
                            Section::make('Profile Information')
                                ->description('Update your personal details and bio.')
                                ->icon(Heroicon::OutlinedUser)
                                ->schema([
                                    $this->getNameFormComponent()
                                        ->label('Full name'),
                                    $this->getEmailFormComponent(),
                                    TextInput::make('username')
                                        ->label('Username')
                                        ->required()
                                        ->alphaDash()
                                        ->maxLength(50)
                                        ->unique(User::class, ignoreRecord: true),
                                    View::make('filament.auth.partials.profile-role-field'),
                                    Textarea::make('bio')
                                        ->label('Bio')
                                        ->rows(4)
                                        ->maxLength(1000)
                                        ->placeholder('Tell us a little about yourself…')
                                        ->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->columnSpan(['xl' => 2])
                                ->extraAttributes(['class' => 'profile-card profile-card--information']),
                            Group::make([
                                Section::make('Profile Photo')
                                    ->description('Drag and drop or browse to upload.')
                                    ->icon(Heroicon::OutlinedPhoto)
                                    ->schema([
                                        $this->getAvatarFormComponent(),
                                    ])
                                    ->extraAttributes(['class' => 'profile-card', 'id' => 'profile-photo']),
                                View::make('filament.auth.partials.profile-quick-actions'),
                            ])
                                ->columnSpan(['xl' => 1]),
                        ]),
                ])
                    ->visible(fn (): bool => $this->activeTab === 'profile'),

                Section::make('Change password')
                    ->description('Use a strong password you do not use elsewhere.')
                    ->icon(Heroicon::OutlinedLockClosed)
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getCurrentPasswordFormComponent(),
                    ])
                    ->columns(1)
                    ->extraAttributes(['class' => 'profile-card'])
                    ->visible(fn (): bool => $this->activeTab === 'security'),

                View::make('filament.auth.partials.profile-tab-preferences')
                    ->visible(fn (): bool => $this->activeTab === 'preferences'),

                View::make('filament.auth.partials.profile-tab-activity')
                    ->visible(fn (): bool => $this->activeTab === 'activity'),
            ]);
    }

    protected function getAvatarFormComponent(): Component
    {
        return FileUpload::make('avatar')
            ->label('')
            ->disk('public')
            ->directory('avatars')
            ->visibility('public')
            ->image()
            ->imageEditor()
            ->imagePreviewHeight('140')
            ->panelLayout('integrated')
            ->removeUploadedFileButtonPosition('right')
            ->uploadButtonPosition('center')
            ->uploadProgressIndicatorPosition('center')
            ->maxSize(4096)
            ->helperText('Drag & drop your image here or browse. JPG, PNG, or WebP · max 4 MB.');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $avatar = $this->getUser()->avatar;

        $data['avatar'] = filled($avatar) ? [$avatar] : [];

        return parent::mutateFormDataBeforeFill($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (array_key_exists('avatar', $data)) {
            $data['avatar'] = $this->normalizeAvatarPath($data['avatar']);
        }

        return parent::mutateFormDataBeforeSave($data);
    }

    /**
     * @param  mixed  $uploaded
     */
    protected function normalizeAvatarPath(mixed $uploaded): ?string
    {
        if ($uploaded === null || $uploaded === '') {
            return null;
        }

        if (is_array($uploaded)) {
            $uploaded = Arr::first($uploaded);
        }

        if (! is_string($uploaded) || $uploaded === '') {
            return null;
        }

        return ltrim($uploaded, '/');
    }

    public function getRoleLabel(): string
    {
        $roles = $this->getUser()->roles->pluck('name');

        return $roles->isNotEmpty()
            ? $roles->implode(', ')
            : 'No role assigned';
    }

    public function getInitials(): string
    {
        return collect(explode(' ', $this->getUser()->name))
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => strtoupper(substr($part, 0, 1)))
            ->implode('') ?: 'U';
    }

    protected function afterSave(): void
    {
        $user = $this->getUser()->fresh(['roles']);

        Filament::auth()->setUser($user);

        $this->dispatch(
            'user-avatar-updated',
            avatarUrl: Filament::getUserAvatarUrl($user),
        );
    }
}
