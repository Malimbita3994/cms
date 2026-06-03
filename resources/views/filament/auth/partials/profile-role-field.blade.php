@php
    $roles = auth()->user()?->roles()->orderBy('name')->get() ?? collect();
@endphp

<div class="profile-role-field">
    <span class="profile-role-field__label">Role</span>
    <div class="profile-role-field__badges" role="list" aria-label="Your roles">
        @forelse ($roles as $role)
            <span class="profile-summary__role-badge" role="listitem">{{ $role->name }}</span>
        @empty
            <span class="profile-summary__role-badge profile-summary__role-badge--muted">No role assigned</span>
        @endforelse
    </div>
    <p class="profile-role-field__hint">Roles are managed by an administrator.</p>
</div>
