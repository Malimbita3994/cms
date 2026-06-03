@php
    $isEdit = isset($this->record) && $this->record;
    $title = $isEdit ? 'Edit user' : 'New user';
    $lead = $isEdit
        ? 'Update account details, roles, and password for ' . $this->record->name . '.'
        : 'Create a new admin account and assign roles.';
@endphp

<x-filament.portfolio-editor
    :title="$title"
    :lead="$lead"
    breadcrumb="Users"
>
    {{ $this->form }}
</x-filament.portfolio-editor>
