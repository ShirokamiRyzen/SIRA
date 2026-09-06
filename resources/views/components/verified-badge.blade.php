@props([
    'user' => null,
    'type' => null,
    'size' => 'sm',
])

@php
    $badgeType = $type ?? ($user ? $user->badgeType() : null);
    $sizeClass = match ($size) {
        'xs' => 'w-3 h-3',
        'md' => 'w-4 h-4',
        'lg' => 'w-5 h-5',
        default => 'w-3.5 h-3.5',
    };
@endphp

@if ($badgeType === 'admin')
    <span class="inline-flex items-center text-amber-500 dark:text-amber-400 shrink-0 select-none align-middle"
          title="Akun Administrator Resmi (Lencana Emas)"
          aria-label="Admin Terverifikasi Gold">
        <svg class="{{ $sizeClass }}" viewBox="0 0 24 24" fill="currentColor">
            <path d="M22.5 12.5c0-1.58-.8-2.95-2-3.77.54-1.51.16-3.22-1-4.38-1.16-1.16-2.87-1.54-4.38-1-1.03-1.44-2.73-2.35-4.62-2.35s-3.59.91-4.62 2.35c-1.51-.54-3.22-.16-4.38 1-1.16 1.16-1.54 2.87-1 4.38-1.2 1.03-2 2.4-2 3.77 0 1.58.8 2.95 2 3.77-.54 1.51-.16 3.22 1 4.38 1.16 1.16 2.87 1.54 4.38 1 1.03 1.44 2.73 2.35 4.62 2.35s3.59-.91 4.62-2.35c1.51.54 3.22.16 4.38-1 1.16-1.16 1.54-2.87 1-4.38 1.2-1.03 2-2.4 2-3.77zm-12.03 4.5L6 12.53l1.41-1.41 3.06 3.06 6.06-6.06 1.41 1.41-7.47 7.47z"/>
        </svg>
    </span>
@elseif ($badgeType === 'verified')
    <span class="inline-flex items-center text-sky-500 dark:text-sky-400 shrink-0 select-none align-middle"
          title="Akun Terverifikasi (Lembaga Daerah / Resmi)"
          aria-label="Akun Terverifikasi Biru">
        <svg class="{{ $sizeClass }}" viewBox="0 0 24 24" fill="currentColor">
            <path d="M22.5 12.5c0-1.58-.8-2.95-2-3.77.54-1.51.16-3.22-1-4.38-1.16-1.16-2.87-1.54-4.38-1-1.03-1.44-2.73-2.35-4.62-2.35s-3.59.91-4.62 2.35c-1.51-.54-3.22-.16-4.38 1-1.16 1.16-1.54 2.87-1 4.38-1.2 1.03-2 2.4-2 3.77 0 1.58.8 2.95 2 3.77-.54 1.51-.16 3.22 1 4.38 1.16 1.16 2.87 1.54 4.38 1 1.03 1.44 2.73 2.35 4.62 2.35s3.59-.91 4.62-2.35c1.51.54 3.22.16 4.38-1 1.16-1.16 1.54-2.87 1-4.38 1.2-1.03 2-2.4 2-3.77zm-12.03 4.5L6 12.53l1.41-1.41 3.06 3.06 6.06-6.06 1.41 1.41-7.47 7.47z"/>
        </svg>
    </span>
@endif
