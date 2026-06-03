<x-filament.portfolio-editor
    :title="$this->getPortfolioFormTitle()"
    :lead="$this->getPortfolioFormLead()"
    :breadcrumb="$this->getPortfolioFormBreadcrumb()"
    :breadcrumb-current="$this instanceof \Filament\Resources\Pages\CreateRecord ? 'Create' : 'Edit'"
>
    {{ $this->form }}
</x-filament.portfolio-editor>
