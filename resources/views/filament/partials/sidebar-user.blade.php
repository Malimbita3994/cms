@php
    $user = auth()->user();
    $name = $user?->name ?? 'Admin';
    $email = $user?->email ?? '';
    $avatarUrl = $user?->getFilamentAvatarUrl();
    $initials = collect(explode(' ', $name))->filter()->take(2)->map(fn (string $p) => strtoupper(substr($p, 0, 1)))->implode('');
@endphp
@if ($user)
    <div class="saas-sidebar-user">
        @if ($avatarUrl)
            <img src="{{ $avatarUrl }}" alt="" class="saas-sidebar-user-avatar saas-sidebar-user-avatar--image" />
        @else
            <span class="saas-sidebar-user-avatar" aria-hidden="true">{{ $initials ?: 'A' }}</span>
        @endif
        <span class="saas-sidebar-user-meta">
            <strong>{{ $name }}</strong>
            <span>{{ $email }}</span>
        </span>
    </div>
@endif
