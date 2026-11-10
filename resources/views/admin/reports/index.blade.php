@extends('layouts.app')

@section('title', 'Manajemen Laporan - SIRA Admin')

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
                Manajemen &amp; Moderasi Laporan
            </h1>
            <p class="text-xs text-sand-600 dark:text-sand-500 mt-0.5">
                Pantau seluruh pengaduan warga, perbarui status tindak lanjut (Aktif / Diproses / Selesai), dan moderasi konten.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-md text-xs font-medium border border-sand-100 dark:border-sand-850 bg-white dark:bg-sand-950 text-sand-975 dark:text-sand-100 hover:bg-sand-50 dark:hover:bg-sand-925 transition shadow-2xs">
                <flux:icon name="users" class="w-3.5 h-3.5 text-amber-500" />
                <span>Manajemen User</span>
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
           class="pb-2.5 px-1 border-b-2 border-sand-975 dark:border-sand-100 text-sand-975 dark:text-sand-100 font-bold flex items-center gap-1.5">
            <flux:icon name="clipboard-document-list" class="w-4 h-4 text-amber-500" />
            <span>Manajemen Laporan ({{ number_format($totalReports) }})</span>
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="pb-2.5 px-3 text-sand-600 dark:text-sand-500 hover:text-sand-975 dark:hover:text-sand-100 transition flex items-center gap-1.5">
            <flux:icon name="users" class="w-4 h-4 text-sand-600 dark:text-sand-500" />
            <span>Manajemen Pengguna</span>
        </a>
    </div>

    <!-- Ringkasan Statistik Kartu (4 Kotak Angka) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="p-4 sm:p-5 rounded-xl bg-white dark:bg-sand-975 border border-sand-100 dark:border-sand-900 shadow-xs space-y-1">
            <div class="flex items-center justify-between text-xs text-sand-600 dark:text-sand-500">
                <span>Total Laporan</span>
                <flux:icon name="document-text" class="w-4 h-4 text-sand-500 dark:text-sand-600" />
            </div>
            <div class="text-2xl font-bold text-sand-975 dark:text-sand-100">
                {{ number_format($totalReports) }}
            </div>
            <p class="text-xs text-sand-600 dark:text-sand-500">Seluruh pengaduan masuk</p>
        </div>

        <div class="p-4 sm:p-5 rounded-xl bg-white dark:bg-sand-975 border border-sand-100 dark:border-sand-900 shadow-xs space-y-1">
            <div class="flex items-center justify-between text-xs text-danger">
                <span>Kritis &amp; Urgent</span>
                <flux:icon name="exclamation-triangle" class="w-4 h-4 text-danger" />
            </div>
            <div class="text-2xl font-bold text-danger">
                {{ number_format($totalCritical + $totalUrgent) }}
            </div>
            <p class="text-xs text-sand-600 dark:text-sand-500">{{ $totalCritical }} Kritis &bull; {{ $totalUrgent }} Mendesak</p>
        </div>

        <div class="p-4 sm:p-5 rounded-xl bg-white dark:bg-sand-975 border border-sand-100 dark:border-sand-900 shadow-xs space-y-1">
            <div class="flex items-center justify-between text-xs text-indigo-600 dark:text-indigo-400">
                <span>Sedang Diproses</span>
                <flux:icon name="arrow-path" class="w-4 h-4 text-indigo-500" />
            </div>
            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                {{ number_format($totalInProgress) }}
            </div>
            <p class="text-xs text-sand-600 dark:text-sand-500">Dalam penanganan dinas</p>
        </div>

        <div class="p-4 sm:p-5 rounded-xl bg-white dark:bg-sand-975 border border-sand-100 dark:border-sand-900 shadow-xs space-y-1">
            <div class="flex items-center justify-between text-xs text-emerald-700 dark:text-emerald-400">
                <span>Tuntas Selesai</span>
                <flux:icon name="check-circle" class="w-4 h-4 text-emerald-600" />
            </div>
            <div class="text-2xl font-bold text-emerald-700 dark:text-emerald-400">
                {{ number_format($totalResolved) }}
            </div>
            <p class="text-xs text-sand-600 dark:text-sand-500">Perbaikan telah tuntas</p>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="p-3.5 sm:p-4 rounded-xl bg-white dark:bg-sand-975 border border-sand-100 dark:border-sand-900 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
        <!-- Filter Tabs -->
        <div class="flex items-center space-x-1.5 overflow-x-auto pb-1 md:pb-0">
            <a href="{{ route('admin.reports.index', ['filter' => 'all', 'q' => $search, 'category' => $category]) }}"
               class="px-2.5 py-1.5 rounded-md transition shrink-0 {{ $filter === 'all' ? 'bg-emerald-600 text-white font-semibold' : 'text-sand-600 dark:text-sand-500 hover:bg-sand-100/50 dark:hover:bg-sand-925' }}">
                Semua
            </a>
            <a href="{{ route('admin.reports.index', ['filter' => 'active', 'q' => $search, 'category' => $category]) }}"
               class="px-2.5 py-1.5 rounded-md transition shrink-0 {{ $filter === 'active' ? 'bg-sand-850 text-white dark:bg-sand-100 dark:text-sand-975 font-semibold' : 'text-sand-600 dark:text-sand-500 hover:bg-sand-100/50 dark:hover:bg-sand-925' }}">
                Aktif
            </a>
            <a href="{{ route('admin.reports.index', ['filter' => 'in_progress', 'q' => $search, 'category' => $category]) }}"
               class="px-2.5 py-1.5 rounded-md transition shrink-0 {{ $filter === 'in_progress' ? 'bg-indigo-600 text-white font-semibold' : 'text-indigo-700 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-950/30 hover:bg-indigo-100' }}">
                Diproses
            </a>
            <a href="{{ route('admin.reports.index', ['filter' => 'resolved', 'q' => $search, 'category' => $category]) }}"
               class="px-2.5 py-1.5 rounded-md transition shrink-0 {{ $filter === 'resolved' ? 'bg-emerald-600 text-white font-semibold' : 'text-emerald-700 dark:text-emerald-400 bg-emerald-50/50 dark:bg-emerald-950/30 hover:bg-emerald-100' }}">
                Selesai
            </a>
            <a href="{{ route('admin.reports.index', ['filter' => 'critical', 'q' => $search, 'category' => $category]) }}"
               class="px-2.5 py-1.5 rounded-md transition shrink-0 {{ $filter === 'critical' ? 'bg-danger text-white font-semibold' : 'text-danger bg-danger-tint dark:bg-danger-deep hover:bg-danger-line' }}">
                Kritis
            </a>
            @if ($totalMultiIssue > 0)
                <a href="{{ route('admin.reports.index', ['filter' => 'multi', 'q' => $search, 'category' => $category]) }}"
                   class="px-2.5 py-1.5 rounded-md transition shrink-0 {{ $filter === 'multi' ? 'bg-violet-700 text-white font-semibold' : 'text-violet-700 dark:text-violet-300 bg-violet-50/60 dark:bg-violet-950/30 hover:bg-violet-100' }}">
                    Multi-Masalah
                </a>
            @endif
        </div>

        <!-- Form Filter Kategori & Pencarian -->
        <form action="{{ route('admin.reports.index') }}" method="GET" class="flex items-center gap-2 max-w-md w-full">
            <input type="hidden" name="filter" value="{{ $filter }}">

            <!-- Select Kategori -->
            <select name="category" onchange="this.form.submit()"
                class="px-2.5 py-2 rounded-md border border-sand-100 dark:border-sand-850 bg-white dark:bg-sand-950 text-sand-975 dark:text-sand-100 focus:outline-none cursor-pointer">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $catKey => $cat)
                    <option value="{{ $catKey }}" {{ $category === $catKey ? 'selected' : '' }}>
                        {{ $cat['label'] }}
                    </option>
                @endforeach
            </select>

            <!-- Kolom Pencarian Teks -->
            <div class="relative flex-1">
                <input type="text"
                       name="q"
                       value="{{ $search }}"
                       placeholder="Cari judul, lokasi, pelapor..."
                       class="w-full px-3 py-2 pr-8 rounded-md border border-sand-100 dark:border-sand-850 bg-sand-25 dark:bg-sand-950 text-sand-975 dark:text-sand-100 placeholder-sand-500 focus:outline-none focus:border-sand-975 dark:focus:border-sand-100">
                @if ($search)
                    <a href="{{ route('admin.reports.index', ['filter' => $filter, 'category' => $category]) }}"
                       class="absolute right-2.5 top-1/2 -translate-y-1/2 text-sand-500 hover:text-sand-975 dark:hover:text-white"
                       title="Hapus pencarian">&times;</a>
                @endif
            </div>

            <button type="submit" class="px-3.5 py-2 rounded-md btn-primary font-medium shrink-0">
                Cari
            </button>
        </form>
    </div>

    <!-- Tabel Data Laporan (Utilitarian Minimalist) -->
    <div class="border border-sand-100 dark:border-sand-900 bg-white dark:bg-sand-975 rounded-xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-sand-25 dark:bg-sand-950 border-b border-sand-100 dark:border-sand-900 text-sand-600 dark:text-sand-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4 font-semibold">Foto &amp; ID</th>
                        <th class="py-3 px-4 font-semibold min-w-[220px]">Laporan &amp; Lokasi</th>
                        <th class="py-3 px-4 font-semibold">Pelapor</th>
                        <th class="py-3 px-4 font-semibold">Kategori &amp; Prioritas</th>
                        <th class="py-3 px-4 font-semibold">Dukungan</th>
                        <th class="py-3 px-4 font-semibold">Status Penanganan</th>
                        <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sand-100 dark:divide-sand-900">
                    @forelse ($reports as $report)
                        <tr class="hover:bg-sand-0 dark:hover:bg-sand-950 transition" id="report-row-{{ $report->id }}">
                            <!-- Kolom Foto Thumbnail -->
                            <td class="py-3.5 px-4">
                                <a href="{{ route('reports.show', $report) }}" class="block w-14 h-12 rounded-md overflow-hidden bg-sand-100 dark:bg-sand-925 border border-sand-100 dark:border-sand-850 shrink-0 group">
                                    <img src="{{ $report->image_base64 }}"
                                         alt="{{ $report->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                </a>
                                <span class="text-xs text-sand-500 dark:text-sand-600 block mt-1">
                                    #{{ $report->id }}
                                </span>
                            </td>

                            <!-- Kolom Judul & Lokasi -->
                            <td class="py-3.5 px-4 font-sans">
                                <a href="{{ route('reports.show', $report) }}"
                                   class="font-bold text-sm text-sand-975 dark:text-sand-100 hover:text-emerald-600 dark:hover:text-emerald-400 line-clamp-2 leading-snug">
                                    {{ $report->title }}
                                </a>
                                <div class="flex items-center gap-1.5 mt-1 text-xs text-sand-600 dark:text-sand-500">
                                    <flux:icon name="map-pin" class="w-3 h-3 text-emerald-600 shrink-0" />
                                    <span class="line-clamp-1">{{ $report->district ?? $report->city ?? $report->formatted_address }}</span>
                                </div>
                                <div class="text-xs text-sand-500 mt-0.5">
                                    {{ $report->created_at->format('d M Y, H:i') }} ({{ $report->created_at->diffForHumans() }})
                                </div>
                            </td>

                            <!-- Kolom Pelapor -->
                            <td class="py-3.5 px-4 text-xs">
                                <div class="font-semibold text-sand-975 dark:text-sand-100 flex items-center space-x-1">
                                    <span>@<span>{{ $report->user->username ?? 'anonim' }}</span></span>
                                    @if ($report->user)
                                        <x-verified-badge :user="$report->user" size="xs" />
                                    @endif
                                </div>
                                <div class="text-xs text-sand-600 dark:text-sand-500 truncate max-w-[140px]">
                                    {{ $report->user->name ?? 'Pengguna' }}
                                </div>
                            </td>

                            <!-- Kolom Kategori & Prioritas -->
                            <td class="py-3.5 px-4 space-y-1">
                                <span class="inline-block px-2 py-0.5 rounded text-xs border border-sand-100 dark:border-sand-850 bg-sand-25 dark:bg-sand-950 text-sand-975 dark:text-sand-100">
                                    {{ $report->category_label }}
                                </span>
                                <div>
                                    @if ($report->rank_tier === 'critical')
                                        <span class="px-2 py-0.5 rounded text-xs uppercase font-bold bg-danger-tint text-danger border border-danger/20">
                                            Kritis
                                        </span>
                                    @elseif ($report->rank_tier === 'urgent')
                                        <span class="px-2 py-0.5 rounded text-xs uppercase font-bold bg-warning-tint text-warning dark:text-warning-soft border border-warning/20">
                                            Mendesak
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-xs uppercase font-semibold bg-success-tint text-success dark:text-success-soft border border-success/20">
                                            Normal
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Kolom Dukungan -->
                            <td class="py-3.5 px-4 text-xs">
                                <div class="font-bold text-sand-975 dark:text-sand-100 flex items-center gap-1">
                                    <flux:icon name="hand-thumb-up" class="w-3.5 h-3.5 text-emerald-600" />
                                    <span>{{ $report->vote_score }} Suara</span>
                                </div>
                                <div class="text-xs text-sand-600 mt-0.5 flex items-center gap-1">
                                    <flux:icon name="chat-bubble-left" class="w-3 h-3 text-sand-500" />
                                    <span>{{ $report->comments_count }} Komentar</span>
                                </div>
                            </td>

                            <!-- Kolom Status Penanganan (Quick Update Dropdown) -->
                            <td class="py-3.5 px-4">
                                <form action="{{ route('reports.updateStatus', $report) }}" method="POST" class="inline-block" onchange="this.submit()">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status"
                                        class="text-xs px-2.5 py-1.5 rounded-md border font-medium focus:outline-none cursor-pointer transition {{ $report->status === 'resolved' ? 'bg-emerald-50 text-emerald-800 border-emerald-300 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800' : ($report->status === 'in_progress' ? 'bg-indigo-50 text-indigo-800 border-indigo-300 dark:bg-indigo-950/50 dark:text-indigo-300 dark:border-indigo-800' : 'bg-sand-25 text-sand-975 border-sand-300 dark:bg-sand-950 dark:text-sand-100 dark:border-sand-800') }}">
                                        <option value="active" {{ $report->status === 'active' ? 'selected' : '' }}>Aktif (Terdaftar)</option>
                                        <option value="in_progress" {{ $report->status === 'in_progress' ? 'selected' : '' }}>Sedang Diproses</option>
                                        <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Tuntas Selesai</option>
                                    </select>
                                </form>
                            </td>

                            <!-- Kolom Aksi -->
                            <td class="py-3.5 px-4 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('reports.show', $report) }}"
                                   class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-md border border-sand-100 dark:border-sand-850 bg-sand-25 dark:bg-sand-950 text-sand-975 dark:text-sand-100 hover:bg-sand-100 dark:hover:bg-sand-925 transition"
                                   title="Buka detail laporan">
                                    <flux:icon name="eye" class="w-3.5 h-3.5" />
                                    <span>Detail</span>
                                </a>

                                <form action="{{ route('reports.destroy', $report) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini? Tindakan ini tidak dapat dibatalkan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-md border border-rose-200 dark:border-rose-900/60 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition cursor-pointer"
                                            title="Hapus laporan ini secara permanen">
                                        <flux:icon name="trash" class="w-3.5 h-3.5" />
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-sand-600 dark:text-sand-500 text-xs">
                                Tidak ada laporan yang sesuai dengan kriteria filter atau pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($reports->hasPages())
            <div class="p-4 border-t border-sand-100 dark:border-sand-900 text-xs">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
