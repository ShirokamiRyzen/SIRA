@extends('layouts.app')

@section('title', 'Manajemen Pengguna - SIRA Admin')

@section('content')
@php
    $filterUrl = fn (string $key) => route('admin.users.index', ['filter' => $key, 'q' => $search]);
    $filters = [
        'all' => 'Semua',
        'verified' => 'Terverifikasi',
        'unverified' => 'Belum verifikasi',
        'admin' => 'Admin',
    ];
    $badge = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold';
    $actionBtn = 'w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition cursor-pointer';
@endphp

<div class="max-w-7xl mx-auto py-2 sm:py-4 space-y-5 sm:space-y-6">
    @include('admin._header', [
        'title' => 'Manajemen Akun Pengguna',
        'subtitle' => 'Kelola lencana verifikasi untuk pemda dan tokoh resmi, serta moderasi akun.',
        'active' => 'users',
    ])

    <!-- Ringkasan angka; tiap angka sekaligus filter -->
    <x-stat-strip class="grid-cols-3">
        <x-stat :value="number_format($totalUsers)" label="Total pengguna" :href="$filterUrl('all')" />
        <x-stat :value="number_format($totalVerified)" label="Terverifikasi" tone="info" :href="$filterUrl('verified')" />
        <x-stat :value="number_format($totalAdmins)" label="Admin" tone="admin" :href="$filterUrl('admin')" />
    </x-stat-strip>

    <!-- Filter & pencarian -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="-mx-4 px-4 sm:mx-0 sm:px-0 flex items-center gap-2 overflow-x-auto no-scrollbar">
            @foreach ($filters as $key => $label)
                <x-filter-pill :href="$filterUrl($key)" :active="$filter === $key">{{ $label }}</x-filter-pill>
            @endforeach
        </div>

        <form action="{{ route('admin.users.index') }}" method="GET" class="relative w-full md:max-w-sm">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <flux:icon name="magnifying-glass" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-sand-400 pointer-events-none" />
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama, @username, email..."
                   class="w-full pl-9 pr-8 py-2 rounded-lg border border-sand-300 dark:border-sand-850 bg-white dark:bg-sand-950 text-sm text-sand-975 dark:text-sand-100 placeholder-sand-400 dark:placeholder-sand-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            @if (!empty($search))
                <a href="{{ route('admin.users.index', ['filter' => $filter]) }}"
                   class="absolute right-3 top-1/2 -translate-y-1/2 text-sand-500 hover:text-sand-975 dark:hover:text-white"
                   title="Hapus pencarian">&times;</a>
            @endif
        </form>
    </div>

    <!-- Daftar pengguna: tabel di layar lebar, kartu bertumpuk di ponsel -->
    <div class="rounded-2xl border border-sand-200 dark:border-sand-900 bg-white dark:bg-sand-975 shadow-2xs overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="hidden md:table-header-group bg-sand-25 dark:bg-sand-950 border-b border-sand-200 dark:border-sand-900 text-xs text-sand-600 dark:text-sand-500">
                <tr>
                    <th scope="col" class="py-3 px-4 font-semibold">Pengguna</th>
                    <th scope="col" class="py-3 px-4 font-semibold">Peran</th>
                    <th scope="col" class="py-3 px-4 font-semibold">Aktivitas</th>
                    <th scope="col" class="py-3 px-4 font-semibold">Terdaftar</th>
                    <th scope="col" class="py-3 px-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sand-200 dark:divide-sand-900">
                @forelse ($users as $user)
                    @php $isSystem = strtolower($user->username) === 'sira'; @endphp
                    <tr class="grid grid-cols-1 gap-3 p-4 md:table-row md:p-0 hover:bg-sand-25 dark:hover:bg-sand-950 transition">
                        <!-- Pengguna: avatar, nama, username, email -->
                        <td class="md:py-3.5 md:px-4">
                            <div class="flex items-center gap-3 min-w-0">
                                @if ($isSystem)
                                    <img src="{{ asset('android-chrome-192x192.png') }}" alt="SIRA AI" class="w-10 h-10 rounded-full object-cover shrink-0 ring-2 ring-emerald-200 dark:ring-emerald-900">
                                @else
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold shrink-0 {{ $user->isAdmin() ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' : ($user->is_verified ? 'bg-info-tint text-info-deep dark:bg-info-deep/40 dark:text-info-soft' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300') }}">
                                        {{ strtoupper(substr($user->username ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-semibold text-sand-975 dark:text-sand-100 truncate">{{ $user->name }}</span>
                                        <x-verified-badge :user="$user" size="xs" />
                                    </div>
                                    <div class="text-xs text-sand-600 dark:text-sand-500 truncate">
                                        <span>{{ '@' . $user->username }}</span>
                                        @if ($user->email && !str_ends_with($user->email, '@sira.local'))
                                            <span title="{{ $user->email }}"> · {{ $user->email }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Peran -->
                        <td class="md:py-3.5 md:px-4 whitespace-nowrap">
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5">
                                @if ($isSystem)
                                    <span class="{{ $badge }} bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Asisten AI</span>
                                @elseif ($user->isAdmin())
                                    <span class="{{ $badge }} bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                        <flux:icon name="shield-check" class="w-3.5 h-3.5 shrink-0" /> Admin
                                    </span>
                                @elseif ($user->is_verified)
                                    <span class="{{ $badge }} bg-info-tint text-info-deep dark:bg-info-deep/40 dark:text-info-soft">
                                        <flux:icon name="check-badge" class="w-3.5 h-3.5 shrink-0" /> Terverifikasi
                                    </span>
                                @else
                                    <span class="{{ $badge }} bg-sand-100 text-sand-700 dark:bg-sand-900 dark:text-sand-300">Warga</span>
                                @endif

                                <!-- Aktivitas & tanggal versi ponsel (kolom terpisah di layar lebar) -->
                                <span class="md:hidden text-xs text-sand-600 dark:text-sand-500">
                                    {{ $user->reports_count }} laporan · {{ $user->comments_count }} komentar · sejak {{ $user->created_at?->translatedFormat('M Y') ?? '-' }}
                                </span>
                            </div>
                        </td>

                        <!-- Aktivitas -->
                        <td class="hidden md:table-cell py-3.5 px-4 whitespace-nowrap text-xs text-sand-600 dark:text-sand-500">
                            <div><span class="font-semibold text-sand-975 dark:text-sand-100 tabular-nums">{{ $user->reports_count }}</span> laporan</div>
                            <div><span class="font-semibold text-sand-975 dark:text-sand-100 tabular-nums">{{ $user->comments_count }}</span> komentar</div>
                        </td>

                        <!-- Terdaftar -->
                        <td class="hidden md:table-cell py-3.5 px-4 whitespace-nowrap text-xs text-sand-600 dark:text-sand-500">
                            {{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}
                        </td>

                        <!-- Aksi -->
                        <td class="md:py-3.5 md:px-4 md:text-right whitespace-nowrap">
                            @if (! $user->isAdmin() && ! $isSystem)
                                <div class="flex md:justify-end gap-2">
                                    <form action="{{ route('admin.users.toggleVerify', $user) }}" method="POST" class="flex-1 md:flex-none flex">
                                        @csrf
                                        @if ($user->is_verified)
                                            <button type="submit" title="Cabut status verifikasi akun ini"
                                                    class="{{ $actionBtn }} border border-sand-200 dark:border-sand-850 bg-white dark:bg-sand-950 text-sand-800 dark:text-sand-200 hover:bg-sand-50 dark:hover:bg-sand-925">
                                                <flux:icon name="x-mark" class="w-4 h-4" />
                                                <span>Cabut Badge</span>
                                            </button>
                                        @else
                                            <button type="submit" title="Berikan lencana verifikasi"
                                                    class="{{ $actionBtn }} border border-info/30 bg-info-tint text-info-deep hover:bg-info-soft/40 dark:border-info/40 dark:bg-info-deep/30 dark:text-info-soft dark:hover:bg-info-deep/50">
                                                <flux:icon name="check-badge" class="w-4 h-4" />
                                                <span>Beri Badge</span>
                                            </button>
                                        @endif
                                    </form>

                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="flex-1 md:flex-none flex"
                                          onsubmit="return confirm(@js('Hapus akun @' . $user->username . ' secara permanen? Semua laporan dan komentarnya ikut terhapus.'));">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus akun ini secara permanen"
                                                class="{{ $actionBtn }} text-danger dark:text-danger-soft hover:bg-danger-tint dark:hover:bg-danger-deep/30">
                                            <flux:icon name="trash" class="w-4 h-4" />
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            @elseif ($user->id === Auth::id())
                                <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">Akun Anda</span>
                            @else
                                <span class="text-xs text-sand-500 dark:text-sand-600">Akun sistem</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="block md:table-cell py-12 px-4 text-center text-sm text-sand-600 dark:text-sand-500">
                            Tidak ada akun yang cocok dengan pencarian ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($users->hasPages())
            <div class="p-4 border-t border-sand-200 dark:border-sand-900 text-sm">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
