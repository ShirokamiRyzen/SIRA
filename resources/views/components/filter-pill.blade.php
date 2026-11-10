{{--
    Pil filter SIRA — satu gaya untuk semua filter cepat: netral saat mati, hijau saat aktif.
    Pakai: <x-filter-pill :href="$url" :active="$isActive">Label</x-filter-pill>
--}}
@props([
    'href',
    'active' => false,
])

<a href="{{ $href }}"
   @if ($active) aria-current="true" @endif
   {{ $attributes->class([
       'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-xs font-semibold whitespace-nowrap shrink-0 transition',
       'bg-emerald-600 border-emerald-600 text-white' => $active,
       'bg-white dark:bg-sand-950 border-sand-200 dark:border-sand-850 text-sand-700 dark:text-sand-300 hover:bg-sand-50 dark:hover:bg-sand-925' => ! $active,
   ]) }}>
    {{ $slot }}
</a>
