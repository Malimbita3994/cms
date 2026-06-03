@php
    /** @var string $title */
    /** @var string|null $status */
    /** @var array<int, array{label: string, html?: string|null, text?: string|null}> $rows */
@endphp

<style>
    .portfolio-record-modal__meta {
        display: grid;
        gap: 12px;
        margin: 0 0 16px;
    }
    .portfolio-record-modal__meta dt {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: rgb(148 163 184);
        margin: 0 0 2px;
    }
    .portfolio-record-modal__meta dd {
        margin: 0;
        color: rgb(241 245 249);
    }
    .portfolio-record-modal__block {
        margin: 0 0 14px;
    }
    .portfolio-record-modal__label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: rgb(148 163 184);
        margin: 0 0 6px;
    }
    .portfolio-record-modal__text {
        margin: 0;
        padding: 12px 14px;
        border-radius: 12px;
        background: rgb(15 23 42);
        border: 1px solid rgb(51 65 85);
        color: rgb(226 232 240);
        line-height: 1.6;
        max-height: 220px;
        overflow-y: auto;
    }
    .portfolio-record-modal__text p { margin: 0 0 0.5rem; }
</style>

<div class="portfolio-record-modal">
    @if ($status)
        <dl class="portfolio-record-modal__meta">
            <div>
                <dt>Status</dt>
                <dd>{{ $status }}</dd>
            </div>
        </dl>
    @endif

    @foreach ($rows as $row)
        <div class="portfolio-record-modal__block">
            <p class="portfolio-record-modal__label">{{ $row['label'] }}</p>
            @if (! empty($row['html']))
                <div class="portfolio-record-modal__text">{!! $row['html'] !!}</div>
            @else
                <p class="portfolio-record-modal__text">{{ $row['text'] ?? '—' }}</p>
            @endif
        </div>
    @endforeach
</div>
