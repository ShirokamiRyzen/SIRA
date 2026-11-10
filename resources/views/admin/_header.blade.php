{{--
    Header & tab panel admin, dipakai bersama halaman Manajemen Laporan dan Manajemen Pengguna.
    @include('admin._header', ['title' => '...', 'subtitle' => '...', 'active' => 'reports'|'users'])
--}}
@php
    $adminTabs = [
        'reports' => ['label' => 'Laporan', 'icon' => 'clipboard-document-list', 'route' => 'admin.reports.index'],
        'users' => ['label' => 'Pengguna', 'icon' => 'users', 'route' => 'admin.users.index'],
    ];
@endphp

<div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div class="space-y-1">
        <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400">
            <flux:icon name="shield-check" class="w-3.5 h-3.5 shrink-0" />
            <span>Panel Admin</span>
        </span>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-sand-975 dark:text-sand-100">
            {{ $title }}
        </h1>
        <p class="text-sm text-sand-600 dark:text-sand-400 leading-relaxed">
            {{ $subtitle }}
        </p>
    </div>

    <!-- Tab admin: kontrol bersegmen -->
    <nav class="inline-flex self-start md:self-auto p-1 rounded-xl bg-sand-100 dark:bg-sand-900 shrink-0" aria-label="Navigasi admin">
        @foreach ($adminTabs as $key => $tab)
            <a href="{{ route($tab['route']) }}"
               @if ($active === $key) aria-current="page" @endif
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ $active === $key ? 'bg-white dark:bg-sand-975 text-sand-975 dark:text-sand-100 shadow-xs' : 'text-sand-600 dark:text-sand-400 hover:text-sand-975 dark:hover:text-sand-100' }}">
                <flux:icon name="{{ $tab['icon'] }}" class="w-4 h-4 shrink-0" />
                <span>{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </nav>
</div>
