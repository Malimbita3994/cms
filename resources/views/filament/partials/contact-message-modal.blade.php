@php
    /** @var \App\Models\ContactMessage $record */
@endphp

<style>
    .contact-message-modal__meta {
        display: grid;
        gap: 12px;
        margin: 0 0 20px;
    }
    .contact-message-modal__meta dt {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: rgb(148 163 184);
        margin: 0 0 2px;
    }
    .contact-message-modal__meta dd {
        margin: 0;
        color: rgb(241 245 249);
    }
    .contact-message-modal__meta a {
        color: rgb(251 191 36);
    }
    .contact-message-modal__label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: rgb(148 163 184);
        margin: 0 0 8px;
    }
    .contact-message-modal__text {
        margin: 0;
        padding: 14px 16px;
        border-radius: 12px;
        background: rgb(15 23 42);
        border: 1px solid rgb(51 65 85);
        color: rgb(226 232 240);
        line-height: 1.6;
        max-height: 320px;
        overflow-y: auto;
    }
    .contact-message-modal__text p { margin: 0 0 0.5rem; }
    .contact-message-modal__text ul,
    .contact-message-modal__text ol { margin: 0.35rem 0; padding-left: 1.25rem; }
    .contact-message-modal__text a { color: rgb(251 191 36); text-decoration: underline; }
</style>

<div class="contact-message-modal">
    <dl class="contact-message-modal__meta">
        <div>
            <dt>Name</dt>
            <dd>{{ $record->name }}</dd>
        </div>
        <div>
            <dt>Email</dt>
            <dd><a href="mailto:{{ $record->email }}">{{ $record->email }}</a></dd>
        </div>
        <div>
            <dt>Received</dt>
            <dd>{{ $record->created_at?->timezone(config('app.timezone'))->format('M j, Y g:i A') ?? '—' }}</dd>
        </div>
        @if ($record->ip_address)
            <div>
                <dt>IP address</dt>
                <dd>{{ $record->ip_address }}</dd>
            </div>
        @endif
    </dl>

    <div class="contact-message-modal__body">
        <p class="contact-message-modal__label">Message</p>
        <div class="contact-message-modal__text">{!! $record->message !!}</div>
    </div>
</div>
