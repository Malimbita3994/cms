<section class="profile-card profile-quick-actions">
    <h3 class="profile-card__title">Quick Actions</h3>
    <p class="profile-card__lead">Shortcuts for common account tasks.</p>
    <ul class="profile-quick-actions__list">
        <li>
            <button
                type="button"
                class="profile-quick-actions__item"
                wire:click="setActiveTab('security')"
            >
                <span class="profile-quick-actions__icon-wrap">
                    <x-filament::icon icon="heroicon-o-lock-closed" class="profile-quick-actions__icon" />
                </span>
                <span class="profile-quick-actions__text">
                    <span class="profile-quick-actions__label">Change password</span>
                    <span class="profile-quick-actions__desc">Update your sign-in credentials</span>
                </span>
                <x-filament::icon icon="heroicon-m-chevron-right" class="profile-quick-actions__chevron" />
            </button>
        </li>
        <li>
            <button
                type="button"
                class="profile-quick-actions__item"
                wire:click="setActiveTab('security')"
            >
                <span class="profile-quick-actions__icon-wrap">
                    <x-filament::icon icon="heroicon-o-computer-desktop" class="profile-quick-actions__icon" />
                </span>
                <span class="profile-quick-actions__text">
                    <span class="profile-quick-actions__label">Manage sessions</span>
                    <span class="profile-quick-actions__desc">Review active sign-in sessions</span>
                </span>
                <x-filament::icon icon="heroicon-m-chevron-right" class="profile-quick-actions__chevron" />
            </button>
        </li>
        <li>
            <button
                type="button"
                class="profile-quick-actions__item"
                wire:click="downloadMyData"
                wire:loading.attr="disabled"
                wire:target="downloadMyData"
            >
                <span class="profile-quick-actions__icon-wrap">
                    <x-filament::icon icon="heroicon-o-arrow-down-tray" class="profile-quick-actions__icon" />
                </span>
                <span class="profile-quick-actions__text">
                    <span class="profile-quick-actions__label">Download my data</span>
                    <span class="profile-quick-actions__desc">Export profile as JSON</span>
                </span>
                <x-filament::icon
                    icon="heroicon-m-chevron-right"
                    class="profile-quick-actions__chevron"
                    wire:loading.remove
                    wire:target="downloadMyData"
                />
                <span class="profile-quick-actions__chevron profile-quick-actions__loading" wire:loading wire:target="downloadMyData">…</span>
            </button>
        </li>
    </ul>
</section>
