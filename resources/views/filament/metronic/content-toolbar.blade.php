@php
    $segment = request()->segment(2);
    $currentLabel = filled($segment) ? str($segment)->replace('-', ' ')->title()->toString() : 'Dashboard';
@endphp

<div class="mt-toolbar mb-4">
    <div class="mt-toolbar__left">
        <h2 class="mt-toolbar__title">{{ $currentLabel }}</h2>
        <p class="mt-toolbar__crumb">Home / Account / {{ $currentLabel }}</p>
    </div>
    <div class="mt-toolbar__right">
        <button type="button" class="mt-toolbar__btn">Reports</button>
        <button type="button" class="mt-toolbar__btn mt-toolbar__btn--primary">+ Add</button>
    </div>
</div>
