@php
    /** @var string $title */
    /** @var string $typeLabel */
    /** @var bool $isPublished */
    /** @var string $category */
    /** @var string $categorySlug */
    /** @var string $slug */
    /** @var bool $isFeatured */
    /** @var string|null $imageUrl */
    /** @var array{name: string, url: string, size: string|null}|null $pdf */
    /** @var string|null $shortDescription */
    /** @var \Illuminate\Support\Carbon|null $publishedAt */
    /** @var \Illuminate\Support\Carbon|null $updatedAt */
    /** @var int $id */
@endphp

<div class="poster-detail-modal">
    <header class="poster-detail-modal__hero">
        <div class="poster-detail-modal__hero-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/>
            </svg>
        </div>
        <div class="poster-detail-modal__hero-copy">
            <h2 class="poster-detail-modal__title">{{ $title }}</h2>
            <p class="poster-detail-modal__type">{{ ucfirst($typeLabel) }}</p>
        </div>
    </header>

    <div class="poster-detail-modal__grid">
        <figure class="poster-detail-modal__preview">
            @if ($imageUrl)
                <img src="{{ $imageUrl }}" alt="" class="poster-detail-modal__preview-img">
                <span class="poster-detail-modal__preview-badge">{{ $category }}</span>
            @else
                <div class="poster-detail-modal__preview-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <path d="m21 15-5-5L5 21"/>
                    </svg>
                    <span>No image</span>
                </div>
            @endif
        </figure>

        <article class="poster-detail-modal__chip poster-detail-modal__chip--status">
            <div class="poster-detail-modal__chip-head">
                <span class="poster-detail-modal__chip-label">Status</span>
                @if ($isPublished)
                    <span class="poster-detail-modal__chip-icon poster-detail-modal__chip-icon--success" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m22 4-10 10.01-3-3"/></svg>
                    </span>
                @endif
            </div>
            <p class="poster-detail-modal__chip-value">
                @if ($isPublished)
                    <span class="poster-detail-modal__status-dot poster-detail-modal__status-dot--live"></span>
                    Live on site
                @else
                    <span class="poster-detail-modal__status-dot poster-detail-modal__status-dot--draft"></span>
                    Draft
                @endif
            </p>
        </article>

        <article class="poster-detail-modal__chip">
            <div class="poster-detail-modal__chip-head">
                <span class="poster-detail-modal__chip-label">Category</span>
                <span class="poster-detail-modal__chip-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                </span>
            </div>
            <p class="poster-detail-modal__chip-value">
                <span class="poster-category-badge poster-category-badge--{{ $categorySlug }}">{{ $category }}</span>
            </p>
        </article>

        <article class="poster-detail-modal__chip">
            <div class="poster-detail-modal__chip-head">
                <span class="poster-detail-modal__chip-label">Featured</span>
                <span class="poster-detail-modal__chip-icon poster-detail-modal__chip-icon--amber" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="{{ $isFeatured ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.75"><path d="M12 2l2.9 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14l-5-4.87 7.1-1.01L12 2z"/></svg>
                </span>
            </div>
            <p class="poster-detail-modal__chip-value">{{ $isFeatured ? 'Yes' : 'No' }}</p>
        </article>

        <article class="poster-detail-modal__chip poster-detail-modal__chip--slug">
            <div class="poster-detail-modal__chip-head">
                <span class="poster-detail-modal__chip-label">Slug</span>
                <span class="poster-detail-modal__chip-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                </span>
            </div>
            <p class="poster-detail-modal__chip-value poster-detail-modal__chip-value--mono">{{ $slug }}</p>
        </article>
    </div>

    @if ($pdf)
        <section class="poster-detail-modal__section">
            <h3 class="poster-detail-modal__section-label">PDF attachment</h3>
            <div class="poster-detail-modal__pdf">
                <div class="poster-detail-modal__pdf-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <path d="M14 2v6h6"/>
                        <path d="M9 13h2a2 2 0 1 0 0-4H9v8"/>
                    </svg>
                </div>
                <div class="poster-detail-modal__pdf-copy">
                    <p class="poster-detail-modal__pdf-name">{{ $pdf['name'] }}</p>
                    <p class="poster-detail-modal__pdf-meta">
                        PDF document
                        @if ($pdf['size'])
                            · {{ $pdf['size'] }}
                        @endif
                    </p>
                </div>
                <button
                    type="button"
                    class="poster-detail-modal__pdf-btn"
                    onclick="document.getElementById('poster-pdf-preview-{{ $id }}')?.showModal()"
                >
                    Preview PDF
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                </button>
            </div>
        </section>

        <dialog
            id="poster-pdf-preview-{{ $id }}"
            class="poster-pdf-preview"
            aria-labelledby="poster-pdf-preview-title-{{ $id }}"
            onclick="if (! event.target.closest('.poster-pdf-preview__dialog')) this.close()"
        >
            <div class="poster-pdf-preview__dialog">
                <header class="poster-pdf-preview__head">
                    <div class="poster-pdf-preview__head-copy">
                        <p class="poster-pdf-preview__eyebrow">PDF preview</p>
                        <h3 id="poster-pdf-preview-title-{{ $id }}" class="poster-pdf-preview__title">{{ $pdf['name'] }}</h3>
                    </div>
                    <button
                        type="button"
                        class="poster-pdf-preview__close"
                        aria-label="Close"
                        onclick="this.closest('dialog')?.close()"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </header>

                <iframe
                    class="poster-pdf-preview__frame"
                    src="{{ $pdf['url'] }}"
                    title="{{ $pdf['name'] }}"
                ></iframe>

                <footer class="poster-pdf-preview__foot">
                    <button
                        type="button"
                        class="poster-pdf-preview__btn poster-pdf-preview__btn--ghost"
                        onclick="this.closest('dialog')?.close()"
                    >
                        Close
                    </button>
                    <a
                        href="{{ $pdf['url'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="poster-pdf-preview__btn poster-pdf-preview__btn--link"
                    >
                        Open in new tab
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6"/><path d="M10 14 21 3"/></svg>
                    </a>
                </footer>
            </div>
        </dialog>
    @endif

    <section class="poster-detail-modal__section">
        <h3 class="poster-detail-modal__section-label">Short description</h3>
        <div class="poster-detail-modal__description">
            @if ($shortDescription)
                <div class="poster-detail-modal__description-body">{!! $shortDescription !!}</div>
            @else
                <p class="poster-detail-modal__description-empty">No short description provided.</p>
            @endif
        </div>
    </section>

    <footer class="poster-detail-modal__meta">
        <div class="poster-detail-modal__meta-item">
            <span class="poster-detail-modal__meta-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                Published
            </span>
            <span class="poster-detail-modal__meta-value">
                {{ $publishedAt?->format('M j, Y') ?? '—' }}
            </span>
        </div>
        <div class="poster-detail-modal__meta-item">
            <span class="poster-detail-modal__meta-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                Updated
            </span>
            <span class="poster-detail-modal__meta-value">
                {{ $updatedAt?->format('M j, Y') ?? '—' }}
            </span>
        </div>
        <div class="poster-detail-modal__meta-item">
            <span class="poster-detail-modal__meta-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 9h16M4 15h16M10 3 8 21M16 3l-2 18"/></svg>
                Post ID
            </span>
            <span class="poster-detail-modal__meta-value poster-detail-modal__meta-value--id">
                #{{ $id }}
                <button
                    type="button"
                    class="poster-detail-modal__copy"
                    title="Copy ID"
                    x-data
                    x-on:click="navigator.clipboard.writeText('{{ $id }}')"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </button>
            </span>
        </div>
    </footer>
</div>
