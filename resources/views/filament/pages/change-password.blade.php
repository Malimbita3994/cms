@push('styles')
    @vite('resources/css/filament/change-password.css')
@endpush

<x-filament-panels::page class="change-password-page">
    <div class="cp-workspace">
        <section class="cp-intro" aria-label="Change password overview">
            <nav class="cp-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ filament()->getUrl() }}" class="cp-breadcrumb__link">Dashboard</a>
                <span class="cp-breadcrumb__sep" aria-hidden="true">/</span>
                <a href="{{ filament()->getProfileUrl() }}" class="cp-breadcrumb__link">My Profile</a>
                <span class="cp-breadcrumb__sep" aria-hidden="true">/</span>
                <span class="cp-breadcrumb__current">Change password</span>
            </nav>

            <div class="cp-intro__row">
                <span class="cp-intro__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </span>
                <div class="cp-intro__copy">
                    <h1 class="cp-intro__title">Change password</h1>
                    <p class="cp-intro__lead">Use a strong, unique password. You stay signed in on this device.</p>
                </div>
            </div>

            <ul class="cp-tips" aria-label="Password tips">
                <li class="cp-tip">
                    <span class="cp-tip__icon cp-tip__icon--amber" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </span>
                    <span class="cp-tip__copy">
                        <strong>Strong password</strong>
                        <span>8+ characters, letters and numbers</span>
                    </span>
                </li>
                <li class="cp-tip">
                    <span class="cp-tip__icon cp-tip__icon--green" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>
                    </span>
                    <span class="cp-tip__copy">
                        <strong>Unique</strong>
                        <span>Do not reuse from other sites</span>
                    </span>
                </li>
                <li class="cp-tip">
                    <span class="cp-tip__icon cp-tip__icon--blue" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    </span>
                    <span class="cp-tip__copy">
                        <strong>Other devices</strong>
                        <span>Sign in again elsewhere if needed</span>
                    </span>
                </li>
            </ul>
        </section>

        <div class="cp-form-card">
            <form
                x-on:submit.prevent="window.portfolioConfirmSave($wire, {
                    title: 'Change password?',
                    text: 'You will need to sign in again with your new password on other devices.',
                    confirmText: 'Update password'
                })"
            >
                {{ $this->form }}

                <div class="cp-form-actions">
                    <x-filament::button
                        type="submit"
                        color="primary"
                        wire:loading.attr="disabled"
                        wire:target="save"
                    >
                        <span wire:loading.remove wire:target="save">Save new password</span>
                        <span wire:loading wire:target="save">Saving…</span>
                    </x-filament::button>
                    <p class="cp-form-footnote">Your session on this browser stays active after the change.</p>
                </div>
            </form>
        </div>

        <a href="{{ filament()->getProfileUrl() }}" class="cp-back-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            Back to profile
        </a>
    </div>
</x-filament-panels::page>
