{{--
    Satu angka di dalam <x-stat-strip>: angka di depan, label di bawah.
    `tone` memberi warna angka sesuai peran; `href` membuatnya bisa diklik (mis. sebagai filter).
--}}
@props([
    'value',
    'label',
    'tone' => 'neutral',
    'href' => null,
])

@php
    $toneClass = [
        'neutral' => 'text-sand-975 dark:text-sand-100',
        'danger' => 'text-danger dark:text-danger-soft',
        'warning' => 'text-warning dark:text-warning-soft',
        'success' => 'text-emerald-700 dark:text-emerald-400',
        'info' => 'text-info dark:text-info-soft',
        'admin' => 'text-amber-700 dark:text-amber-400',
    ][$tone] ?? 'text-sand-975 dark:text-sand-100';
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif
    {{ $attributes->class([
        'px-4 py-4 sm:px-6 sm:py-5 bg-white dark:bg-sand-950',
        'hover:bg-sand-25 dark:hover:bg-sand-925 transition' => $href,
    ]) }}>
    <div class="text-2xl sm:text-3xl font-bold tabular-nums {{ $toneClass }}">{{ $value }}</div>
    <div class="text-xs sm:text-sm text-sand-600 dark:text-sand-500 mt-1">{{ $label }}</div>
</{{ $tag }}>
