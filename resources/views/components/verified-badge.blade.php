@props([
    'user' => null,
    'type' => null,
    'size' => 'sm',
])

@php
    $badgeType = $type ?? ($user ? $user->badgeType() : null);
    $sizeClass = match ($size) {
        'xs' => 'w-3.5 h-3.5',
        'sm' => 'w-4 h-4',
        'md' => 'w-4.5 h-4.5',
        'lg' => 'w-5 h-5',
        default => 'w-3.5 h-3.5',
    };
@endphp

@if ($badgeType === 'admin')
    <span class="inline-flex items-center text-amber-500 dark:text-amber-400 shrink-0 select-none align-middle"
          title="Akun Administrator Resmi (Lencana Emas)"
          aria-label="Admin Terverifikasi Gold">
        <flux:icon name="shield-check" variant="solid" class="{{ $sizeClass }}" />
    </span>
@elseif ($badgeType === 'verified')
    <span class="inline-flex items-center text-sky-500 dark:text-sky-400 shrink-0 select-none align-middle"
          title="Akun Terverifikasi (Lembaga Daerah / Resmi)"
          aria-label="Akun Terverifikasi Biru">
        <flux:icon name="check-badge" variant="solid" class="{{ $sizeClass }}" />
    </span>
@endif
