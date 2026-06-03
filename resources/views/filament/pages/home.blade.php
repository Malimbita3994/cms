@push('styles')
    @vite('resources/css/filament/home-editor.css')
@endpush

@push('scripts')
    @vite('resources/js/filament/portfolio-alerts.js')
@endpush

<x-filament-panels::page class="home-editor-page">
    <div class="home-editor">
        <header class="home-editor__header">
            <div class="home-editor__header-top">
                <div class="home-editor__header-main">
                    <h1 class="home-editor__title">Home</h1>
                    <p class="home-editor__lead">
                        Manage the content displayed on your homepage.
                    </p>
                </div>

                @if (count($this->getCachedHeaderActions()))
                    <div class="home-editor__header-actions">
                        <x-filament::actions :actions="$this->getCachedHeaderActions()" />
                    </div>
                @endif
            </div>

            <nav class="home-editor__breadcrumbs" aria-label="Breadcrumb">
                <a href="{{ filament()->getUrl() }}" class="home-editor__breadcrumb-link">Dashboard</a>
                <span class="home-editor__breadcrumb-sep" aria-hidden="true">&gt;</span>
                <span>Home</span>
                <span class="home-editor__breadcrumb-sep" aria-hidden="true">&gt;</span>
                <span class="home-editor__breadcrumb-current">Edit</span>
            </nav>
        </header>

        <form
            class="home-editor__form"
            x-on:submit.prevent="window.portfolioConfirmSave($wire)"
        >
            <div class="home-editor__sections">
                {{ $this->form }}
            </div>

            <footer class="home-editor__footer">
                <x-filament::button
                    type="button"
                    color="gray"
                    tag="button"
                    class="home-editor__btn-cancel"
                    x-on:click.prevent="window.portfolioConfirmCancel($wire, {
                        title: 'Leave without saving?',
                        text: 'Unsaved changes will be lost.',
                        confirmText: 'Leave'
                    })"
                >
                    Cancel
                </x-filament::button>

                <x-filament::button
                    type="submit"
                    icon="heroicon-o-check"
                    class="home-editor__btn-save"
                    wire:loading.attr="disabled"
                    wire:target="save"
                >
                    <span wire:loading.remove wire:target="save">Save changes</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </x-filament::button>
            </footer>
        </form>
    </div>
</x-filament-panels::page>
