@php
    /** @var \App\Models\CaseStudy $record */
    $imageUrl = $record->image
        ? (str_starts_with($record->image, 'http') ? $record->image : asset(ltrim($record->image, '/')))
        : null;
@endphp

<style>
    .case-study-modal__meta {
        display: grid;
        gap: 12px;
        margin: 0 0 20px;
    }
    .case-study-modal__meta dt {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: rgb(148 163 184);
        margin: 0 0 2px;
    }
    .case-study-modal__meta dd {
        margin: 0;
        color: rgb(241 245 249);
    }
    .case-study-modal__image {
        margin: 0 0 20px;
        max-height: 200px;
        overflow: hidden;
        border-radius: 12px;
        border: 1px solid rgb(51 65 85);
        background: rgb(15 23 42);
    }
    .case-study-modal__image img {
        display: block;
        width: 100%;
        height: auto;
        max-height: 200px;
        object-fit: cover;
    }
    .case-study-modal__label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: rgb(148 163 184);
        margin: 0 0 8px;
    }
    .case-study-modal__text {
        margin: 0 0 16px;
        padding: 14px 16px;
        border-radius: 12px;
        background: rgb(15 23 42);
        border: 1px solid rgb(51 65 85);
        color: rgb(226 232 240);
        line-height: 1.6;
        max-height: 200px;
        overflow-y: auto;
    }
    .case-study-modal__text--impact {
        color: rgb(110 231 183);
    }
    .case-study-modal__text p { margin: 0 0 0.5rem; }
    .case-study-modal__stack {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .case-study-modal__stack li {
        padding: 4px 10px;
        border-radius: 9999px;
        background: rgb(30 41 59);
        color: rgb(203 213 225);
        font-size: 0.75rem;
    }
</style>

<div class="case-study-modal">
    <dl class="case-study-modal__meta">
        <div>
            <dt>Order</dt>
            <dd>{{ $record->sort_order }}</dd>
        </div>
        <div>
            <dt>Status</dt>
            <dd>{{ ($record->is_published ?? true) ? 'Published on site' : 'Draft (hidden)' }}</dd>
        </div>
        <div>
            <dt>Updated</dt>
            <dd>{{ $record->updated_at?->timezone(config('app.timezone'))->format('M j, Y g:i A') ?? '—' }}</dd>
        </div>
    </dl>

    @if ($imageUrl)
        <div class="case-study-modal__image">
            <img src="{{ $imageUrl }}" alt="" />
        </div>
    @endif

    <div>
        <p class="case-study-modal__label">Description</p>
        <div class="case-study-modal__text">{!! $record->desc !!}</div>
    </div>

    <div>
        <p class="case-study-modal__label">Impact</p>
        <div class="case-study-modal__text case-study-modal__text--impact">{!! $record->impact !!}</div>
    </div>

    @if (is_array($record->stack) && count($record->stack) > 0)
        <div>
            <p class="case-study-modal__label">Technology stack</p>
            <ul class="case-study-modal__stack">
                @foreach ($record->stack as $tech)
                    <li>{{ $tech }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
