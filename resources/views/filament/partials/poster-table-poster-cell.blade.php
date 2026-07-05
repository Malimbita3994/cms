@php
    use App\Support\PortfolioAsset;
    use Illuminate\Support\Str;

    /** @var \App\Models\Poster $record */
    $record = $getRecord();
    $imageRelative = PortfolioAsset::toUploadState($record->image);
    $imageUrl = $imageRelative !== null ? url('/media/'.ltrim($imageRelative, '/')) : null;
    $summary = Str::limit(strip_tags($record->short_description ?? ''), 48);
    $slugPath = '/updates/'.$record->slug;
@endphp

<div class="poster-row-poster">
    <div class="poster-row-poster__thumb">
        @if ($imageUrl)
            <img src="{{ $imageUrl }}" alt="" class="poster-row-poster__img">
        @else
            <span class="poster-row-poster__placeholder" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
            </span>
        @endif
    </div>
    <div class="poster-row-poster__copy">
        <p class="poster-row-poster__title">{{ $record->title }}</p>
        @if ($summary !== '')
            <p class="poster-row-poster__summary">{{ $summary }}</p>
        @else
            <p class="poster-row-poster__slug">{{ $slugPath }}</p>
        @endif
    </div>
</div>
