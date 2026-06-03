<x-filament.portfolio-editor
    :title="$this->getPortfolioFormTitle()"
    :lead="$this->getPortfolioFormLead()"
    :breadcrumb="$this->getPortfolioFormBreadcrumb()"
    :read-only="true"
    :edit-url="$this->getPortfolioEditUrl()"
    :index-url="$this->getPortfolioIndexUrl()"
    breadcrumb-current="View"
>
    {{ $this->form }}
</x-filament.portfolio-editor>
