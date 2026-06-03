@php
    $user = $this->getUser();
    $avatarUrl = $user->getFilamentAvatarUrl();
    $roleName = $user->primaryRoleName();
    $initials = $this->getInitials();
    $tabs = [
        'profile' => ['label' => 'Profile', 'icon' => 'heroicon-o-user-circle'],
        'security' => ['label' => 'Security', 'icon' => 'heroicon-o-shield-check'],
        'preferences' => ['label' => 'Preferences', 'icon' => 'heroicon-o-adjustments-horizontal'],
        'activity' => ['label' => 'Activity', 'icon' => 'heroicon-o-clock'],
    ];
@endphp

@push('styles')
    @vite('resources/css/filament/profile.css')
@endpush

<x-filament-panels::page class="profile-page">
    <div class="profile-workspace">
        <nav class="profile-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ filament()->getUrl() }}" class="profile-breadcrumb__link">Dashboard</a>
            <span class="profile-breadcrumb__sep" aria-hidden="true">/</span>
            <span class="profile-breadcrumb__current">My Profile</span>
        </nav>

        <header class="profile-page-header">
            <div class="profile-page-header__intro">
                <h1 class="profile-page-header__title">My Profile</h1>
                <p class="profile-page-header__lead">
                    View and manage your account information and preferences.
                </p>
            </div>
            <x-filament::button
                type="button"
                color="primary"
                icon="heroicon-o-pencil-square"
                class="profile-page-header__edit-btn"
                x-on:click="
                    $wire.setActiveTab('profile');
                    $nextTick(() => {
                        setTimeout(() => {
                            document.getElementById('profile-form')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 80);
                    });
                "
            >
                Edit profile
            </x-filament::button>
        </header>

        <section class="profile-summary profile-card" aria-label="Account summary">
            <div class="profile-summary__main">
                <div class="profile-summary__identity">
                    <div class="profile-summary__avatar-wrap">
                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="" class="profile-summary__avatar" />
                        @else
                            <span class="profile-summary__avatar profile-summary__avatar--initials">{{ $initials }}</span>
                        @endif
                        <button
                            type="button"
                            class="profile-summary__avatar-edit"
                            title="Change profile photo"
                            x-on:click="
                                $wire.setActiveTab('profile');
                                $nextTick(() => {
                                    setTimeout(() => {
                                        document.getElementById('profile-photo')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                    }, 80);
                                });
                            "
                        >
                            <x-filament::icon icon="heroicon-m-camera" class="profile-summary__avatar-edit-icon" />
                        </button>
                    </div>

                    <div class="profile-summary__meta">
                        <div class="profile-summary__name-row">
                            <h2 class="profile-summary__name">{{ $user->name }}</h2>
                            @if ($roleName)
                                <span class="profile-summary__role-badge">{{ $roleName }}</span>
                            @endif
                        </div>
                        <p class="profile-summary__contact">
                            <x-filament::icon icon="heroicon-m-envelope" class="profile-summary__contact-icon" />
                            {{ $user->email }}
                        </p>
                        <p class="profile-summary__contact">
                            <x-filament::icon icon="heroicon-m-calendar-days" class="profile-summary__contact-icon" />
                            Joined {{ $user->created_at?->format('M j, Y') ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>

            <aside class="profile-summary__aside" aria-label="Account details">
                <div class="profile-summary__stats">
                    <div class="profile-summary__stat">
                        <p class="profile-summary__stat-label">Role</p>
                        <p class="profile-summary__stat-value">{{ $roleName ?? '—' }}</p>
                    </div>
                    <div class="profile-summary__stat">
                        <p class="profile-summary__stat-label">Username</p>
                        <p class="profile-summary__stat-value">{{ $user->username ?? '—' }}</p>
                    </div>
                    <div class="profile-summary__stat">
                        <p class="profile-summary__stat-label">Status</p>
                        <p class="profile-summary__stat-value">
                            <span @class([
                                'profile-status-badge',
                                'profile-status-badge--active' => $user->isActive(),
                                'profile-status-badge--inactive' => ! $user->isActive(),
                            ])>
                                {{ $user->isActive() ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                    <div class="profile-summary__stat">
                        <p class="profile-summary__stat-label">Last login</p>
                        <p class="profile-summary__stat-value">{{ $user->last_login_at?->format('M j, Y g:i A') ?? '—' }}</p>
                    </div>
                </div>
            </aside>
        </section>

        <nav class="profile-tabs" aria-label="Profile sections">
            @foreach ($tabs as $key => $tab)
                <button
                    type="button"
                    @class(['profile-tabs__tab', 'profile-tabs__tab--active' => $activeTab === $key])
                    wire:click="setActiveTab('{{ $key }}')"
                    wire:loading.attr="disabled"
                    wire:target="setActiveTab"
                >
                    <x-filament::icon :icon="$tab['icon']" class="profile-tabs__icon" />
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </nav>

        <div id="profile-form" class="profile-form-anchor" aria-hidden="true"></div>

        <form wire:submit="save" class="profile-form">
            <div
                class="profile-form__body"
                wire:loading.class="profile-form__body--loading"
                wire:target="setActiveTab, save"
            >
                {{ $this->form }}
            </div>

            @if (in_array($activeTab, ['profile', 'security'], true))
                <footer class="profile-form__footer">
                    <span class="profile-form__footer-hint">
                        @if ($activeTab === 'security')
                            Leave password fields blank to keep your current password.
                        @else
                            Changes apply to your admin account and sidebar avatar.
                        @endif
                    </span>

                    <x-filament::button
                        type="button"
                        color="gray"
                        tag="button"
                        wire:click="resetProfileForm"
                        wire:loading.attr="disabled"
                        wire:target="save"
                    >
                        Reset
                    </x-filament::button>

                    <x-filament::button
                        type="submit"
                        icon="heroicon-o-check"
                        class="profile-form__save"
                        wire:loading.attr="disabled"
                        wire:target="save"
                    >
                        <span wire:loading.remove wire:target="save">Save changes</span>
                        <span wire:loading wire:target="save">Saving…</span>
                    </x-filament::button>
                </footer>
            @endif
        </form>
    </div>
</x-filament-panels::page>
