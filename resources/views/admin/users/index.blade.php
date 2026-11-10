@extends('layouts.app')

@section('title', 'Manajemen Pengguna - SIRA Admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header Halaman Admin -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-sand-100 dark:border-sand-900">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded-md text-xs font-semibold uppercase tracking-wider bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">
                    Administrator
                </span>
                <span class="text-xs text-sand-600 dark:text-sand-500">Panel Kontrol</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-sand-975 dark:text-sand-100 tracking-tight mt-1.5">
                Manajemen Akun Pengguna
            </h1>
            <p class="text-xs text-sand-600 dark:text-sand-500 mt-0.5">
                Kelola hak akses pengguna, lencana verifikasi pemda/tokoh resmi, dan moderasi akun platform.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.reports.index') }}"
               class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-md text-xs font-medium border border-sand-100 dark:border-sand-850 bg-white dark:bg-sand-950 text-sand-975 dark:text-sand-100 hover:bg-sand-50 dark:hover:bg-sand-925 transition shadow-2xs">
                <flux:icon name="clipboard-document-list" class="w-3.5 h-3.5 text-amber-500" />
                <span>Manajemen Laporan</span>
            </a>
            <a href="{{ route('reports.index') }}"
               class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-md text-xs font-medium border border-sand-100 dark:border-sand-850 bg-white dark:bg-sand-950 text-sand-975 dark:text-sand-100 hover:bg-sand-50 dark:hover:bg-sand-925 transition shadow-2xs">
                <flux:icon name="document-text" class="w-3.5 h-3.5 text-sand-600 dark:text-sand-500" />
                <span>Lihat Feed Publik</span>
            </a>
            <x-report-cta size="sm" />
        </div>
    </div>

    <!-- Navigasi Tab Admin Sub-Menu -->
    <div class="flex items-center space-x-2 border-b border-sand-100 dark:border-sand-900 text-xs">
        <a href="{{ route('admin.reports.index') }}"
           class="pb-2.5 px-3 text-sand-600 dark:text-sand-500 hover:text-sand-975 dark:hover:text-sand-100 transition flex items-center gap-1.5">
            <flux:icon name="clipboard-document-list" class="w-4 h-4 text-sand-600 dark:text-sand-500" />
            <span>Manajemen Laporan</span>
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="pb-2.5 px-1 border-b-2 border-sand-975 dark:border-sand-100 text-sand-975 dark:text-sand-100 font-bold flex items-center gap-1.5">
            <flux:icon name="users" class="w-4 h-4 text-amber-500" />
            <span>Manajemen Pengguna ({{ number_format($totalUsers) }})</span>
        </a>
    </div>

    <!-- Ringkasan Statistik Kartu (Utilitarian Minimalist) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        <div class="p-4 sm:p-5 rounded-xl bg-white dark:bg-sand-975 border border-sand-100 dark:border-sand-900 shadow-xs space-y-1.5">
            <div class="flex items-center justify-between text-xs text-sand-600 dark:text-sand-500">
                <span>Total Pengguna</span>
                <flux:icon name="users" class="w-4 h-4 text-sand-500 dark:text-sand-600" />
            </div>
            <div class="text-2xl font-bold text-sand-975 dark:text-sand-100">
                {{ number_format($totalUsers) }}
            </div>
            <p class="text-xs text-sand-600 dark:text-sand-500">Terdaftar di platform SIRA</p>
        </div>

        <div class="p-4 sm:p-5 rounded-xl bg-white dark:bg-sand-975 border border-sand-100 dark:border-sand-900 shadow-xs space-y-1.5">
            <div class="flex items-center justify-between text-xs text-sky-600 dark:text-sky-400">
                <span>Akun Terverifikasi</span>
                <flux:icon name="check-badge" class="w-4 h-4 text-sky-500" />
            </div>
            <div class="text-2xl font-bold text-sky-600 dark:text-sky-400">
                {{ number_format($totalVerified) }}
            </div>
            <p class="text-xs text-sand-600 dark:text-sand-500">Lembaga Pemda &amp; Tokoh Resmi</p>
        </div>

        <div class="p-4 sm:p-5 rounded-xl bg-white dark:bg-sand-975 border border-sand-100 dark:border-sand-900 shadow-xs space-y-1.5">
            <div class="flex items-center justify-between text-xs text-amber-600 dark:text-amber-400">
                <span>Administrator</span>
                <flux:icon name="shield-check" class="w-4 h-4 text-amber-500" />
            </div>
            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                {{ number_format($totalAdmins) }}
            </div>
            <p class="text-xs text-sand-600 dark:text-sand-500">Hak akses sistem penuh</p>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="p-3 sm:p-4 rounded-xl bg-white dark:bg-sand-975 border border-sand-100 dark:border-sand-900 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
        <!-- Filter Tabs -->
        <div class="flex items-center space-x-1.5 overflow-x-auto pb-1 md:pb-0">
            <a href="{{ route('admin.users.index', ['filter' => 'all', 'q' => $search]) }}"
               class="px-3 py-1.5 rounded-md transition {{ $filter === 'all' ? 'bg-emerald-600 text-white font-semibold' : 'text-sand-600 dark:text-sand-500 hover:text-sand-975 dark:hover:text-sand-100 hover:bg-sand-100/50 dark:hover:bg-sand-925' }}">
                Semua
            </a>
            <a href="{{ route('admin.users.index', ['filter' => 'verified', 'q' => $search]) }}"
               class="px-3 py-1.5 rounded-md transition flex items-center space-x-1.5 {{ $filter === 'verified' ? 'bg-sky-600 text-white font-semibold' : 'text-sand-600 dark:text-sand-500 hover:text-sand-975 dark:hover:text-sand-100 hover:bg-sand-100/50 dark:hover:bg-sand-925' }}">
                <flux:icon name="check-badge" class="w-3.5 h-3.5 text-sky-500 {{ $filter === 'verified' ? 'text-white' : '' }}" />
                <span>Terverifikasi</span>
            </a>
            <a href="{{ route('admin.users.index', ['filter' => 'unverified', 'q' => $search]) }}"
               class="px-3 py-1.5 rounded-md transition {{ $filter === 'unverified' ? 'bg-sand-850 text-white dark:bg-sand-100 dark:text-sand-975 font-semibold' : 'text-sand-600 dark:text-sand-500 hover:text-sand-975 dark:hover:text-sand-100 hover:bg-sand-100/50 dark:hover:bg-sand-925' }}">
                Belum Verifikasi
            </a>
            <a href="{{ route('admin.users.index', ['filter' => 'admin', 'q' => $search]) }}"
               class="px-3 py-1.5 rounded-md transition flex items-center space-x-1.5 {{ $filter === 'admin' ? 'bg-amber-600 text-white font-semibold' : 'text-sand-600 dark:text-sand-500 hover:text-sand-975 dark:hover:text-sand-100 hover:bg-sand-100/50 dark:hover:bg-sand-925' }}">
                <flux:icon name="shield-check" class="w-3.5 h-3.5 text-amber-500 {{ $filter === 'admin' ? 'text-white' : '' }}" />
                <span>Admin</span>
            </a>
        </div>

        <!-- Form Pencarian -->
        <form action="{{ route('admin.users.index') }}" method="GET" class="relative max-w-sm w-full">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <input type="text"
                   name="q"
                   value="{{ $search }}"
                   placeholder="Cari nama, @username, email..."
                   class="w-full pl-9 pr-8 py-2 rounded-md text-xs border border-sand-100 dark:border-sand-850 bg-sand-25 dark:bg-sand-950 text-sand-975 dark:text-sand-100 focus:outline-none focus:border-sand-975 dark:focus:border-sand-100">
            <div class="absolute left-3 top-2.5 text-sand-600 pointer-events-none">
                <flux:icon name="magnifying-glass" class="w-3.5 h-3.5" />
            </div>
            @if (!empty($search))
                <a href="{{ route('admin.users.index', ['filter' => $filter]) }}"
                   class="absolute right-2.5 top-2.5 text-sand-600 hover:text-sand-975 dark:hover:text-white text-xs">
                    &times;
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel Daftar Pengguna (Refined Minimalist Editorial) -->
    <div class="rounded-xl border border-sand-100 dark:border-sand-900 bg-white dark:bg-sand-975 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-sand-25 dark:bg-sand-950 border-b border-sand-100 dark:border-sand-900 text-sand-600 dark:text-sand-500 uppercase tracking-wider text-xs">
                    <tr>
                        <th scope="col" class="px-5 py-3.5">Pengguna</th>
                        <th scope="col" class="px-4 py-3.5">Role &amp; Lencana</th>
                        <th scope="col" class="px-4 py-3.5 text-center">Aktivitas</th>
                        <th scope="col" class="px-4 py-3.5">Terdaftar</th>
                        <th scope="col" class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sand-100/80 dark:divide-sand-925 text-sand-975 dark:text-sand-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-sand-25/70 dark:hover:bg-sand-950 transition-colors">
                            <!-- Kolom Pengguna (Avatar, Nama, Username, Email) -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center space-x-3">
                                    @if (strtolower($user->username) === 'sira')
                                        <img src="{{ asset('android-chrome-192x192.png') }}" alt="SIRA AI" class="w-9 h-9 rounded-md object-cover shrink-0 border border-indigo-500/30">
                                    @else
                                        <div class="w-9 h-9 rounded-md flex items-center justify-center text-xs font-bold shrink-0 {{ $user->isAdmin() ? 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20' : ($user->is_verified ? 'bg-sky-500/10 text-sky-700 dark:text-sky-400 border border-sky-500/20' : 'bg-sand-50 dark:bg-sand-925 text-sand-700 dark:text-sand-400 border border-sand-100 dark:border-sand-850') }}">
                                            {{ strtoupper(substr($user->username ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="flex items-center space-x-1.5 flex-wrap">
                                            <span class="font-semibold text-sm text-sand-975 dark:text-sand-100 truncate">
                                                {{ $user->name }}
                                            </span>
                                            <x-verified-badge :user="$user" size="xs" />
                                        </div>
                                        <div class="text-xs text-sand-600 dark:text-sand-500 flex items-center space-x-2 mt-0.5">
                                            <span>{{ '@' . $user->username }}</span>
                                            @if ($user->email && !str_ends_with($user->email, '@sira.local'))
                                                <span>&bull;</span>
                                                <span class="truncate max-w-[200px]" title="{{ $user->email }}">{{ $user->email }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Kolom Role & Lencana -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if ($user->isAdmin())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/20">
                                        <flux:icon name="shield-check" class="w-3.5 h-3.5 text-amber-500 shrink-0" />
                                        <span>Admin</span>
                                    </span>
                                @elseif ($user->is_verified)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-sky-500/10 text-sky-700 dark:text-sky-300 border border-sky-500/20">
                                        <flux:icon name="check-badge" class="w-3.5 h-3.5 text-sky-500 shrink-0" />
                                        <span>Terverifikasi</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs text-sand-600 dark:text-sand-500 bg-sand-50 dark:bg-sand-925">
                                        Warga
                                    </span>
                                @endif
                            </td>

                            <!-- Kolom Aktivitas -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <div class="inline-flex items-center justify-center gap-2.5 text-xs text-sand-600 dark:text-sand-500">
                                    <span class="inline-flex items-center gap-1" title="{{ $user->reports_count }} laporan dibuat">
                                        <flux:icon name="document-text" class="w-3.5 h-3.5 text-sand-500 dark:text-sand-600" />
                                        <span class="font-medium text-sand-975 dark:text-sand-100">{{ $user->reports_count }}</span>
                                    </span>
                                    <span class="text-sand-300 dark:text-sand-800">&bull;</span>
                                    <span class="inline-flex items-center gap-1" title="{{ $user->comments_count }} komentar ditulis">
                                        <flux:icon name="chat-bubble-left" class="w-3.5 h-3.5 text-sand-500 dark:text-sand-600" />
                                        <span class="font-medium text-sand-975 dark:text-sand-100">{{ $user->comments_count }}</span>
                                    </span>
                                </div>
                            </td>

                            <!-- Kolom Tanggal Terdaftar -->
                            <td class="px-4 py-3.5 text-sand-600 dark:text-sand-500 text-xs whitespace-nowrap">
                                {{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}
                            </td>

                            <!-- Kolom Aksi -->
                            <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap text-xs">
                                @if (! $user->isAdmin() && strtolower($user->username) !== 'sira')
                                    <!-- Toggle Verifikasi -->
                                    <form action="{{ route('admin.users.toggleVerify', $user) }}" method="POST" class="inline">
                                        @csrf
                                        @if ($user->is_verified)
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-medium text-amber-700 dark:text-amber-300 hover:bg-amber-500/10 border border-amber-500/20 hover:border-amber-500/40 transition active:scale-95 cursor-pointer"
                                                    title="Cabut status verifikasi akun ini">
                                                <flux:icon name="x-mark" class="w-3.5 h-3.5 text-amber-500" />
                                                <span>Cabut Badge</span>
                                            </button>
                                        @else
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-medium text-sand-600 dark:text-sand-500 hover:text-sky-600 dark:hover:text-sky-400 hover:bg-sky-500/10 border border-sand-100 dark:border-sand-850 hover:border-sky-500/30 transition active:scale-95 cursor-pointer"
                                                    title="Berikan lencana verifikasi biru resmi">
                                                <flux:icon name="check-badge" class="w-3.5 h-3.5 text-sky-500" />
                                                <span>Beri Badge</span>
                                            </button>
                                        @endif
                                    </form>

                                    <!-- Hapus Akun -->
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun @<span>{{ $user->username }}</span> secara permanen? Seluruh postingan dan komentar pengguna ini akan ikut terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-medium text-sand-600 dark:text-sand-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-500/10 border border-transparent hover:border-rose-500/20 transition active:scale-95 cursor-pointer"
                                                title="Hapus akun ini secara permanen">
                                            <flux:icon name="trash" class="w-3.5 h-3.5 text-rose-500" />
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                @elseif ($user->id === Auth::id())
                                    <span class="inline-block text-xs text-emerald-600 dark:text-emerald-400 font-medium px-2 py-1">
                                        Akun Anda
                                    </span>
                                @else
                                    <span class="inline-block text-xs text-sand-600 dark:text-sand-600 px-2 py-1">
                                        Sistem
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sand-600 dark:text-sand-500 text-xs">
                                Tidak ada akun pengguna yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($users->hasPages())
            <div class="px-5 py-3.5 border-t border-sand-100 dark:border-sand-900 bg-sand-25 dark:bg-sand-950">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
