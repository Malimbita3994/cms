<x-filament.portfolio-editor
    title="Contact"
    lead="Manage public contact details and read messages sent from your site contact form."
    breadcrumb="Contact"
>
    {{ $this->form }}

    <div class="contact-inbox">
        <div class="home-editor-card contact-inbox-card">
            {{ $this->table }}
        </div>
    </div>
</x-filament.portfolio-editor>
