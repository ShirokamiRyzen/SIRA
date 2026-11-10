{{--
    Tombol aksi utama "lapor" SIRA — satu-satunya sumber gaya tombol ini.
    Pakai: <x-report-cta />, <x-report-cta size="sm" />, <x-report-cta label="..." class="w-full" />

    Catatan: jangan oper `hidden` lewat `class`. Komponen ini memakai `inline-flex`, dan di build
    Tailwind v4 `.inline-flex` muncul setelah `.hidden` sehingga `hidden` kalah. Untuk
    menyembunyikan secara responsif, bungkus dengan elemen lain (mis. <div class="hidden md:block">).
--}}
@props([
    'size' => 'md',
    'label' => 'Laporkan Masalah',
])

@php
    $sizeClasses = [
        'sm' => 'h-9 px-3.5 rounded-lg',
        'md' => 'py-3 px-3 sm:px-5 rounded-xl',
    ][$size] ?? 'py-3 px-3 sm:px-5 rounded-xl';
@endphp

<a href="{{ route('reports.create') }}"
   {{ $attributes->class([
       'inline-flex items-center justify-center gap-1.5 whitespace-nowrap btn-primary text-sm font-semibold shadow-sm transition',
       $sizeClasses,
   ]) }}>
    <flux:icon name="camera" class="w-4 h-4 shrink-0" />
    <span>{{ $label }}</span>
</a>
