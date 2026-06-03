@props([
    'title',
    'lead',
    'breadcrumb',
    'readOnly' => false,
    'editUrl' => null,
    'indexUrl' => null,
    'breadcrumbCurrent' => 'Edit',
])

@push('styles')
    @vite('resources/css/filament/home-editor.css')
@endpush

@unless ($readOnly)
    @push('scripts')
        @vite('resources/js/filament/portfolio-alerts.js')
    @endpush
@endunless

<x-filament-panels::page class="home-editor-page">
    <div class="home-editor">
        <header class="home-editor__header">
            <div class="home-editor__header-main">
                <h1 class="home-editor__title">{{ $title }}</h1>
                <p class="home-editor__lead">{{ $lead }}</p>
            </div>

            <nav class="home-editor__breadcrumbs" aria-label="Breadcrumb">
                <a href="{{ filament()->getUrl() }}" class="home-editor__breadcrumb-link">Dashboard</a>
                <span class="home-editor__breadcrumb-sep" aria-hidden="true">&gt;</span>
                <span>{{ $breadcrumb }}</span>
                <span class="home-editor__breadcrumb-sep" aria-hidden="true">&gt;</span>
                <span class="home-editor__breadcrumb-current">{{ $breadcrumbCurrent }}</span>
            </nav>
        </header>

        @if ($readOnly)
            <div class="home-editor__form home-editor__form--view">
                <div class="home-editor__sections">
                    {{ $slot }}
                </div>

                <footer class="home-editor__footer">
                    @if ($indexUrl)
                        <x-filament::button
                            type="button"
                            color="gray"
                            tag="a"
                            :href="$indexUrl"
                            class="home-editor__btn-cancel"
                        >
                            Back to list
                        </x-filament::button>
                    @endif

                    @if ($editUrl)
                        <x-filament::button
                            type="button"
                            icon="heroicon-o-pencil-square"
                            tag="a"
                            :href="$editUrl"
                            class="home-editor__btn-save"
                        >
                            Edit
                        </x-filament::button>
                    @endif
                </footer>
            </div>
        @else
            <form
                class="home-editor__form"
                x-on:submit.prevent="window.portfolioConfirmSave($wire)"
            >
                <div class="home-editor__sections">
                    {{ $slot }}
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
                        wire:target="save,create"
                    >
                        <span wire:loading.remove wire:target="save,create">Save changes</span>
                        <span wire:loading wire:target="save,create">Saving…</span>
                    </x-filament::button>
                </footer>
            </form>
        @endif
    </div>
</x-filament-panels::page>
