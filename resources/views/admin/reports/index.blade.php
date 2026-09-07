@extends('layouts.app')

@section('title', 'Manajemen Laporan - SIRA Admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header Halaman Admin -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#EAEAEA] dark:border-[#222222]">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded-[4px] text-[10px] font-mono font-semibold uppercase tracking-wider bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">
                    Administrator
                </span>
                <span class="text-xs text-[#787774] dark:text-[#888888] font-mono">Panel Kontrol</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#111111] dark:text-[#EDEDEC] tracking-tight mt-1.5">
                Manajemen &amp; Moderasi Laporan
            </h1>
            <p class="text-xs text-[#787774] dark:text-[#888888] mt-0.5">
                Pantau seluruh pengaduan warga, perbarui status tindak lanjut (Aktif / Diproses / Selesai), dan moderasi konten.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-[6px] text-xs font-mono font-medium border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#161615] text-[#111111] dark:text-[#EDEDEC] hover:bg-[#F7F6F3] dark:hover:bg-[#1F1F1E] transition shadow-2xs">
                <flux:icon name="users" class="w-3.5 h-3.5 text-amber-500" />
                <span>Manajemen User</span>
            </a>
            <a href="{{ route('reports.index') }}"
               class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-[6px] text-xs font-mono font-medium border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#161615] text-[#111111] dark:text-[#EDEDEC] hover:bg-[#F7F6F3] dark:hover:bg-[#1F1F1E] transition shadow-2xs">
                <flux:icon name="document-text" class="w-3.5 h-3.5 text-[#787774] dark:text-[#9B9B97]" />
                <span>Lihat Feed Publik</span>
            </a>
            <a href="{{ route('reports.create') }}"
               class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-[6px] text-xs font-medium bg-[#111111] hover:bg-[#2A2A2A] text-white dark:bg-[#EDEDEC] dark:text-[#111111] dark:hover:bg-white transition shadow-2xs">
                <span>+ Buat Laporan</span>
            </a>
        </div>
    </div>

    <!-- Navigasi Tab Admin Sub-Menu -->
    <div class="flex items-center space-x-2 border-b border-[#EAEAEA] dark:border-[#222222] font-mono text-xs">
        <a href="{{ route('admin.reports.index') }}"
           class="pb-2.5 px-1 border-b-2 border-[#111111] dark:border-[#EDEDEC] text-[#111111] dark:text-[#EDEDEC] font-bold flex items-center gap-1.5">
            <flux:icon name="clipboard-document-list" class="w-4 h-4 text-amber-500" />
            <span>Manajemen Laporan ({{ number_format($totalReports) }})</span>
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="pb-2.5 px-3 text-[#787774] dark:text-[#8E8D8A] hover:text-[#111111] dark:hover:text-[#EDEDEC] transition flex items-center gap-1.5">
            <flux:icon name="users" class="w-4 h-4 text-[#787774] dark:text-[#8E8D8A]" />
            <span>Manajemen Pengguna</span>
        </a>
    </div>

    <!-- Ringkasan Statistik Kartu (4 Kotak Angka) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 font-mono">
        <div class="p-4 sm:p-5 rounded-[8px] bg-white dark:bg-[#141414] border border-[#EAEAEA] dark:border-[#222222] shadow-xs space-y-1">
            <div class="flex items-center justify-between text-xs text-[#787774] dark:text-[#8E8D8A]">
                <span>Total Laporan</span>
                <flux:icon name="document-text" class="w-4 h-4 text-[#999999] dark:text-[#666666]" />
            </div>
            <div class="text-2xl font-bold text-[#111111] dark:text-[#EDEDEC]">
                {{ number_format($totalReports) }}
            </div>
            <p class="text-[11px] text-[#787774] dark:text-[#8E8D8A]">Seluruh pengaduan masuk</p>
        </div>

        <div class="p-4 sm:p-5 rounded-[8px] bg-white dark:bg-[#141414] border border-[#EAEAEA] dark:border-[#222222] shadow-xs space-y-1">
            <div class="flex items-center justify-between text-xs text-[#9F2F2D]">
                <span>Kritis &amp; Urgent</span>
                <flux:icon name="exclamation-triangle" class="w-4 h-4 text-[#9F2F2D]" />
            </div>
            <div class="text-2xl font-bold text-[#9F2F2D]">
                {{ number_format($totalCritical + $totalUrgent) }}
            </div>
            <p class="text-[11px] text-[#787774] dark:text-[#8E8D8A]">{{ $totalCritical }} Kritis &bull; {{ $totalUrgent }} Mendesak</p>
        </div>

        <div class="p-4 sm:p-5 rounded-[8px] bg-white dark:bg-[#141414] border border-[#EAEAEA] dark:border-[#222222] shadow-xs space-y-1">
            <div class="flex items-center justify-between text-xs text-indigo-600 dark:text-indigo-400">
                <span>Sedang Diproses</span>
                <flux:icon name="arrow-path" class="w-4 h-4 text-indigo-500" />
            </div>
            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                {{ number_format($totalInProgress) }}
            </div>
            <p class="text-[11px] text-[#787774] dark:text-[#8E8D8A]">Dalam penanganan dinas</p>
        </div>

        <div class="p-4 sm:p-5 rounded-[8px] bg-white dark:bg-[#141414] border border-[#EAEAEA] dark:border-[#222222] shadow-xs space-y-1">
            <div class="flex items-center justify-between text-xs text-emerald-700 dark:text-emerald-400">
                <span>Tuntas Selesai</span>
                <flux:icon name="check-circle" class="w-4 h-4 text-emerald-600" />
            </div>
            <div class="text-2xl font-bold text-emerald-700 dark:text-emerald-400">
                {{ number_format($totalResolved) }}
            </div>
            <p class="text-[11px] text-[#787774] dark:text-[#8E8D8A]">Perbaikan telah tuntas</p>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="p-3.5 sm:p-4 rounded-[8px] bg-white dark:bg-[#141414] border border-[#EAEAEA] dark:border-[#222222] shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3 font-mono text-xs">
        <!-- Filter Tabs -->
        <div class="flex items-center space-x-1.5 overflow-x-auto pb-1 md:pb-0">
            <a href="{{ route('admin.reports.index', ['filter' => 'all', 'q' => $search, 'category' => $category]) }}"
               class="px-2.5 py-1.5 rounded-[6px] transition shrink-0 {{ $filter === 'all' ? 'bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111] font-semibold' : 'text-[#787774] dark:text-[#9B9B97] hover:bg-[#EAEAEA]/50 dark:hover:bg-[#202020]' }}">
                Semua
            </a>
            <a href="{{ route('admin.reports.index', ['filter' => 'active', 'q' => $search, 'category' => $category]) }}"
               class="px-2.5 py-1.5 rounded-[6px] transition shrink-0 {{ $filter === 'active' ? 'bg-[#2A2A2A] text-white dark:bg-[#EDEDEC] dark:text-[#111111] font-semibold' : 'text-[#787774] dark:text-[#9B9B97] hover:bg-[#EAEAEA]/50 dark:hover:bg-[#202020]' }}">
                Aktif
            </a>
            <a href="{{ route('admin.reports.index', ['filter' => 'in_progress', 'q' => $search, 'category' => $category]) }}"
               class="px-2.5 py-1.5 rounded-[6px] transition shrink-0 {{ $filter === 'in_progress' ? 'bg-indigo-600 text-white font-semibold' : 'text-indigo-700 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-950/30 hover:bg-indigo-100' }}">
                Diproses
            </a>
            <a href="{{ route('admin.reports.index', ['filter' => 'resolved', 'q' => $search, 'category' => $category]) }}"
               class="px-2.5 py-1.5 rounded-[6px] transition shrink-0 {{ $filter === 'resolved' ? 'bg-emerald-600 text-white font-semibold' : 'text-emerald-700 dark:text-emerald-400 bg-emerald-50/50 dark:bg-emerald-950/30 hover:bg-emerald-100' }}">
                Selesai
            </a>
            <a href="{{ route('admin.reports.index', ['filter' => 'critical', 'q' => $search, 'category' => $category]) }}"
               class="px-2.5 py-1.5 rounded-[6px] transition shrink-0 {{ $filter === 'critical' ? 'bg-[#9F2F2D] text-white font-semibold' : 'text-[#9F2F2D] bg-[#FDEBEC] dark:bg-[#311617] hover:bg-[#F9D6D8]' }}">
                Kritis
            </a>
            @if ($totalMultiIssue > 0)
                <a href="{{ route('admin.reports.index', ['filter' => 'multi', 'q' => $search, 'category' => $category]) }}"
                   class="px-2.5 py-1.5 rounded-[6px] transition shrink-0 {{ $filter === 'multi' ? 'bg-violet-700 text-white font-semibold' : 'text-violet-700 dark:text-violet-300 bg-violet-50/60 dark:bg-violet-950/30 hover:bg-violet-100' }}">
                    Multi-Masalah
                </a>
            @endif
        </div>

        <!-- Form Filter Kategori & Pencarian -->
        <form action="{{ route('admin.reports.index') }}" method="GET" class="flex items-center gap-2 max-w-md w-full">
            <input type="hidden" name="filter" value="{{ $filter }}">

            <!-- Select Kategori -->
            <select name="category" onchange="this.form.submit()"
                class="px-2.5 py-2 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-[#111111] dark:text-[#EDEDEC] focus:outline-none cursor-pointer">
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
                       class="w-full px-3 py-2 pr-8 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-[#FBFBFA] dark:bg-[#181818] text-[#111111] dark:text-[#EDEDEC] placeholder-[#999999] focus:outline-none focus:border-[#111111] dark:focus:border-[#EDEDEC]">
                @if ($search)
                    <a href="{{ route('admin.reports.index', ['filter' => $filter, 'category' => $category]) }}"
                       class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[#999999] hover:text-[#111111] dark:hover:text-white"
                       title="Hapus pencarian">&times;</a>
                @endif
            </div>

            <button type="submit" class="px-3.5 py-2 rounded-[6px] bg-[#111111] hover:bg-[#2A2A2A] text-white dark:bg-[#EDEDEC] dark:text-[#111111] dark:hover:bg-white font-medium shrink-0">
                Cari
            </button>
        </form>
    </div>

    <!-- Tabel Data Laporan (Utilitarian Minimalist) -->
    <div class="border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] rounded-[8px] overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#FBFBFA] dark:bg-[#181818] border-b border-[#EAEAEA] dark:border-[#222222] text-[#787774] dark:text-[#8E8D8A] font-mono uppercase tracking-wider">
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
                <tbody class="divide-y divide-[#EAEAEA] dark:divide-[#222222]">
                    @forelse ($reports as $report)
                        <tr class="hover:bg-[#FDFDFD] dark:hover:bg-[#181818] transition" id="report-row-{{ $report->id }}">
                            <!-- Kolom Foto Thumbnail -->
                            <td class="py-3.5 px-4">
                                <a href="{{ route('reports.show', $report) }}" class="block w-14 h-12 rounded-[6px] overflow-hidden bg-[#EAEAEA] dark:bg-[#202020] border border-[#EAEAEA] dark:border-[#282828] shrink-0 group">
                                    <img src="{{ $report->image_base64 }}"
                                         alt="{{ $report->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                </a>
                                <span class="font-mono text-[10px] text-[#999999] dark:text-[#666666] block mt-1">
                                    #{{ $report->id }}
                                </span>
                            </td>

                            <!-- Kolom Judul & Lokasi -->
                            <td class="py-3.5 px-4 font-sans">
                                <a href="{{ route('reports.show', $report) }}"
                                   class="font-bold text-sm text-[#111111] dark:text-[#EDEDEC] hover:text-emerald-600 dark:hover:text-emerald-400 line-clamp-2 leading-snug">
                                    {{ $report->title }}
                                </a>
                                <div class="flex items-center gap-1.5 mt-1 text-[11px] font-mono text-[#787774] dark:text-[#8E8D8A]">
                                    <flux:icon name="map-pin" class="w-3 h-3 text-emerald-600 shrink-0" />
                                    <span class="line-clamp-1">{{ $report->district ?? $report->city ?? $report->formatted_address }}</span>
                                </div>
                                <div class="text-[10px] font-mono text-[#999999] mt-0.5">
                                    {{ $report->created_at->format('d M Y, H:i') }} ({{ $report->created_at->diffForHumans() }})
                                </div>
                            </td>

                            <!-- Kolom Pelapor -->
                            <td class="py-3.5 px-4 font-mono text-xs">
                                <div class="font-semibold text-[#111111] dark:text-[#EDEDEC] flex items-center space-x-1">
                                    <span>@<span>{{ $report->user->username ?? 'anonim' }}</span></span>
                                    @if ($report->user)
                                        <x-verified-badge :user="$report->user" size="xs" />
                                    @endif
                                </div>
                                <div class="text-[11px] text-[#787774] dark:text-[#888888] truncate max-w-[140px]">
                                    {{ $report->user->name ?? 'Pengguna' }}
                                </div>
                            </td>

                            <!-- Kolom Kategori & Prioritas -->
                            <td class="py-3.5 px-4 font-mono space-y-1">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] border border-[#EAEAEA] dark:border-[#282828] bg-[#FBFBFA] dark:bg-[#181818] text-[#111111] dark:text-[#EDEDEC]">
                                    {{ $report->category_label }}
                                </span>
                                <div>
                                    @if ($report->rank_tier === 'critical')
                                        <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-[#FDEBEC] text-[#9F2F2D] border border-[#9F2F2D]/20">
                                            Kritis
                                        </span>
                                    @elseif ($report->rank_tier === 'urgent')
                                        <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-[#FBF3DB] text-[#956400] dark:text-[#E0BE69] border border-[#956400]/20">
                                            Mendesak
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] uppercase font-semibold bg-[#EDF3EC] text-[#346538] dark:text-[#82C78A] border border-[#346538]/20">
                                            Normal
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Kolom Dukungan -->
                            <td class="py-3.5 px-4 font-mono text-xs">
                                <div class="font-bold text-[#111111] dark:text-[#EDEDEC] flex items-center gap-1">
                                    <flux:icon name="hand-thumb-up" class="w-3.5 h-3.5 text-emerald-600" />
                                    <span>{{ $report->vote_score }} Suara</span>
                                </div>
                                <div class="text-[11px] text-[#787774] mt-0.5 flex items-center gap-1">
                                    <flux:icon name="chat-bubble-left" class="w-3 h-3 text-[#999999]" />
                                    <span>{{ $report->comments_count }} Komentar</span>
                                </div>
                            </td>

                            <!-- Kolom Status Penanganan (Quick Update Dropdown) -->
                            <td class="py-3.5 px-4 font-mono">
                                <form action="{{ route('reports.updateStatus', $report) }}" method="POST" class="inline-block" onchange="this.submit()">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status"
                                        class="text-xs px-2.5 py-1.5 rounded-[6px] border font-mono font-medium focus:outline-none cursor-pointer transition {{ $report->status === 'resolved' ? 'bg-emerald-50 text-emerald-800 border-emerald-300 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800' : ($report->status === 'in_progress' ? 'bg-indigo-50 text-indigo-800 border-indigo-300 dark:bg-indigo-950/50 dark:text-indigo-300 dark:border-indigo-800' : 'bg-[#FBFBFA] text-[#111111] border-[#CCCCCC] dark:bg-[#181818] dark:text-[#EDEDEC] dark:border-[#333333]') }}">
                                        <option value="active" {{ $report->status === 'active' ? 'selected' : '' }}>Aktif (Terdaftar)</option>
                                        <option value="in_progress" {{ $report->status === 'in_progress' ? 'selected' : '' }}>Sedang Diproses</option>
                                        <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Tuntas Selesai</option>
                                    </select>
                                </form>
                            </td>

                            <!-- Kolom Aksi -->
                            <td class="py-3.5 px-4 text-right font-mono space-x-2 whitespace-nowrap">
                                <a href="{{ route('reports.show', $report) }}"
                                   class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-[4px] border border-[#EAEAEA] dark:border-[#282828] bg-[#FBFBFA] dark:bg-[#181818] text-[#111111] dark:text-[#EDEDEC] hover:bg-[#F0F0EF] dark:hover:bg-[#202020] transition"
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
                                            class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-[4px] border border-rose-200 dark:border-rose-900/60 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition cursor-pointer"
                                            title="Hapus laporan ini secara permanen">
                                        <flux:icon name="trash" class="w-3.5 h-3.5" />
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-[#787774] dark:text-[#888888] font-mono text-xs">
                                Tidak ada laporan yang sesuai dengan kriteria filter atau pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($reports->hasPages())
            <div class="p-4 border-t border-[#EAEAEA] dark:border-[#222222] font-mono text-xs">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
