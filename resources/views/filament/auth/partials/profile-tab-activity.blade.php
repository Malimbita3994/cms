@php
    $user = $this->getUser();
    $events = [
        ['label' => 'Last login', 'value' => $user->last_login_at?->format('M j, Y g:i A') ?? 'Not recorded yet'],
        ['label' => 'Account created', 'value' => $user->created_at?->format('M j, Y g:i A') ?? '—'],
        ['label' => 'Profile updated', 'value' => $user->updated_at?->format('M j, Y g:i A') ?? '—'],
        ['label' => 'Email verified', 'value' => $user->email_verified_at?->format('M j, Y') ?? 'Not verified'],
    ];
@endphp

<section class="profile-card profile-activity">
    <h3 class="profile-card__title">Recent activity</h3>
    <p class="profile-card__lead">A timeline of important account events.</p>
    <ul class="profile-activity__list">
        @foreach ($events as $event)
            <li class="profile-activity__item">
                <span class="profile-activity__label">{{ $event['label'] }}</span>
                <span class="profile-activity__value">{{ $event['value'] }}</span>
            </li>
        @endforeach
    </ul>
</section>
