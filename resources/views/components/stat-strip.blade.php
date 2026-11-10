{{--
    Strip ringkasan angka: satu permukaan bersekat tipis, isinya <x-stat>.
    Jumlah kolom diatur pemanggil, mis. <x-stat-strip class="grid-cols-2 sm:grid-cols-4">.
--}}
<div {{ $attributes->class('grid gap-px rounded-2xl border border-sand-200 dark:border-sand-900 bg-sand-200 dark:bg-sand-900 shadow-2xs overflow-hidden') }}>
    {{ $slot }}
</div>
