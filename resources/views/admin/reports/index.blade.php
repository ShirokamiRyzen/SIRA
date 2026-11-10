@extends('layouts.app')

@section('title', 'Manajemen Laporan - SIRA Admin')

@section('content')
@php
    // Status yang bisa diatur admin; label diambil dari satu sumber (Report::statusLabels()).
    $statusOptions = \Illuminate\Support\Arr::only(\App\Models\Report::statusLabels(), ['active', 'in_progress', 'resolved']);
    $filterUrl = fn (string $key) => route('admin.reports.index', ['filter' => $key, 'q' => $search, 'category' => $category]);
    $filters = [
        'all' => 'Semua',
        'active' => $statusOptions['active'],
        'in_progress' => $statusOptions['in_progress'],
        'resolved' => $statusOptions['resolved'],
        'critical' => 'Kritis',
        'urgent' => 'Mendesak',
    ];
    if ($totalMultiIssue > 0) {
        $filters['multi'] = 'Multi-Masalah';
    }
    $fieldClass = 'px-3 py-2 rounded-lg border border-sand-300 dark:border-sand-850 bg-white dark:bg-sand-950 text-sm text-sand-975 dark:text-sand-100 placeholder-sand-400 dark:placeholder-sand-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500';
    $badge = 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold';
@endphp

<div class="max-w-7xl mx-auto py-2 sm:py-4 space-y-5 sm:space-y-6">
    @include('admin._header', [
        'title' => 'Manajemen & Moderasi Laporan',
        'subtitle' => 'Pantau laporan warga, perbarui status penanganan, dan hapus konten yang melanggar.',
        'active' => 'reports',
    ])

    <!-- Ringkasan angka; tiap angka sekaligus filter -->
    <x-stat-strip class="grid-cols-2 lg:grid-cols-4">
        <x-stat :value="number_format($totalReports)" label="Total laporan" :href="$filterUrl('all')" />
        <x-stat :value="number_format($totalCritical + $totalUrgent)" label="Kritis & mendesak" tone="danger" :href="$filterUrl('critical')" />
        <x-stat :value="number_format($totalInProgress)" :label="$statusOptions['in_progress']" tone="info" :href="$filterUrl('in_progress')" />
        <x-stat :value="number_format($totalResolved)" :label="$statusOptions['resolved']" tone="success" :href="$filterUrl('resolved')" />
    </x-stat-strip>

    <!-- Filter & pencarian -->
    <div class="space-y-3">
        <div class="-mx-4 px-4 sm:mx-0 sm:px-0 flex items-center gap-2 overflow-x-auto no-scrollbar">
            @foreach ($filters as $key => $label)
                <x-filter-pill :href="$filterUrl($key)" :active="$filter === $key">{{ $label }}</x-filter-pill>
            @endforeach
        </div>

        <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
            <input type="hidden" name="filter" value="{{ $filter }}">

            <select name="category" onchange="this.form.submit()" class="{{ $fieldClass }} cursor-pointer sm:w-56">
                <option value="">Semua kategori</option>
                @foreach ($categories as $catKey => $cat)
                    <option value="{{ $catKey }}" {{ $category === $catKey ? 'selected' : '' }}>{{ $cat['label'] }}</option>
                @endforeach
            </select>

            <div class="relative flex-1">
                <flux:icon name="magnifying-glass" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-sand-400 pointer-events-none" />
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari judul, lokasi, pelapor..."
                       class="{{ $fieldClass }} w-full pl-9 pr-8">
                @if ($search)
                    <a href="{{ route('admin.reports.index', ['filter' => $filter, 'category' => $category]) }}"
                       class="absolute right-3 top-1/2 -translate-y-1/2 text-sand-500 hover:text-sand-975 dark:hover:text-white"
                       title="Hapus pencarian">&times;</a>
                @endif
            </div>

            <button type="submit" class="px-4 py-2 rounded-lg btn-primary text-sm font-semibold shrink-0">Cari</button>
        </form>
    </div>

    <!-- Daftar laporan: tabel di layar lebar, kartu bertumpuk di ponsel -->
    <div class="rounded-2xl border border-sand-200 dark:border-sand-900 bg-white dark:bg-sand-975 shadow-2xs overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="hidden md:table-header-group bg-sand-25 dark:bg-sand-950 border-b border-sand-200 dark:border-sand-900 text-xs text-sand-600 dark:text-sand-500">
                <tr>
                    <th class="py-3 px-4 font-semibold">Laporan</th>
                    <th class="py-3 px-4 font-semibold">Pelapor</th>
                    <th class="py-3 px-4 font-semibold">Kategori & prioritas</th>
                    <th class="py-3 px-4 font-semibold">Dukungan</th>
                    <th class="py-3 px-4 font-semibold">Status</th>
                    <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sand-200 dark:divide-sand-900">
                @forelse ($reports as $report)
                    <tr class="grid grid-cols-1 gap-3 p-4 md:table-row md:p-0 hover:bg-sand-25 dark:hover:bg-sand-950 transition" id="report-row-{{ $report->id }}">
                        <!-- Laporan: foto, judul, lokasi, waktu -->
                        <td class="md:py-3.5 md:px-4 md:align-top">
                            <div class="flex gap-3 min-w-0 md:min-w-[260px]">
                                <a href="{{ route('reports.show', $report) }}" class="block w-16 h-14 rounded-lg overflow-hidden bg-sand-100 dark:bg-sand-925 border border-sand-200 dark:border-sand-850 shrink-0">
                                    <img src="{{ $report->image_base64 }}" alt="{{ $report->title }}" class="w-full h-full object-cover">
                                </a>
                                <div class="min-w-0">
                                    <a href="{{ route('reports.show', $report) }}"
                                       class="font-semibold text-sand-975 dark:text-sand-100 hover:text-emerald-700 dark:hover:text-emerald-400 line-clamp-2 leading-snug">
                                        {{ $report->title }}
                                    </a>
                                    <div class="flex items-center gap-1 mt-1 text-xs text-sand-600 dark:text-sand-500">
                                        <flux:icon name="map-pin" class="w-3 h-3 shrink-0" />
                                        <span class="line-clamp-1">{{ $report->district ?? $report->city ?? $report->formatted_address }}</span>
                                    </div>
                                    <div class="text-xs text-sand-500 mt-0.5">
                                        #{{ $report->id }} · {{ $report->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Pelapor -->
                        <td class="md:py-3.5 md:px-4 md:align-top">
                            <div class="flex items-center gap-1 text-sm font-semibold text-sand-975 dark:text-sand-100">
                                <span class="md:hidden text-xs font-normal text-sand-500">oleh</span>
                                <span>@<span>{{ $report->user->username ?? 'anonim' }}</span></span>
                                @if ($report->user)
                                    <x-verified-badge :user="$report->user" size="xs" />
                                @endif
                            </div>
                            <div class="hidden md:block text-xs text-sand-600 dark:text-sand-500 truncate max-w-[140px]">
                                {{ $report->user->name ?? 'Pengguna' }}
                            </div>
                        </td>

                        <!-- Kategori & prioritas -->
                        <td class="md:py-3.5 md:px-4 md:align-top">
                            <div class="flex flex-wrap md:flex-col md:items-start gap-1.5">
                                <span class="{{ $badge }} {{ $report->category_meta['badge_class'] }}">{{ $report->category_label }}</span>
                                @if ($report->rank_tier === 'critical')
                                    <span class="{{ $badge }} bg-danger-tint text-danger-deep dark:bg-danger-deep/40 dark:text-danger-soft">Kritis</span>
                                @elseif ($report->rank_tier === 'urgent')
                                    <span class="{{ $badge }} bg-warning-tint text-warning-deep dark:bg-warning-deep/40 dark:text-warning-soft">Mendesak</span>
                                @endif
                            </div>
                        </td>

                        <!-- Dukungan -->
                        <td class="md:py-3.5 md:px-4 md:align-top whitespace-nowrap">
                            <div class="flex md:flex-col gap-3 md:gap-0.5 text-xs text-sand-600 dark:text-sand-500">
                                <span class="inline-flex items-center gap-1">
                                    <flux:icon name="hand-thumb-up" class="w-3.5 h-3.5" />
                                    <span class="font-semibold text-sand-975 dark:text-sand-100 tabular-nums">{{ $report->vote_score }}</span> dukungan
                                </span>
                                <span class="inline-flex items-center gap-1">
                                    <flux:icon name="chat-bubble-left" class="w-3.5 h-3.5" />
                                    <span class="tabular-nums">{{ $report->comments_count }}</span> komentar
                                </span>
                            </div>
                        </td>

                        <!-- Status: ubah langsung; warna ditentukan data-status -->
                        <td class="md:py-3.5 md:px-4 md:align-top">
                            <form action="{{ route('reports.updateStatus', $report) }}" method="POST" onchange="this.submit()">
                                @csrf
                                @method('PATCH')
                                <select name="status" data-status="{{ $report->status }}" aria-label="Status laporan #{{ $report->id }}"
                                    class="w-full md:w-auto text-sm px-3 py-2 rounded-lg border font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer transition bg-white text-sand-800 border-sand-300 dark:bg-sand-950 dark:text-sand-200 dark:border-sand-800 data-[status=in_progress]:bg-info-tint data-[status=in_progress]:text-info-deep data-[status=in_progress]:border-info/30 dark:data-[status=in_progress]:bg-info-deep/30 dark:data-[status=in_progress]:text-info-soft data-[status=resolved]:bg-success-tint data-[status=resolved]:text-success-deep data-[status=resolved]:border-success/30 dark:data-[status=resolved]:bg-success-deep/30 dark:data-[status=resolved]:text-success-soft">
                                    @foreach ($statusOptions as $value => $label)
                                        <option value="{{ $value }}" {{ $report->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>

                        <!-- Aksi -->
                        <td class="md:py-3.5 md:px-4 md:align-top md:text-right whitespace-nowrap">
                            <div class="flex md:justify-end gap-2">
                                <a href="{{ route('reports.show', $report) }}"
                                   class="flex-1 md:flex-none inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-sand-200 dark:border-sand-850 bg-white dark:bg-sand-950 text-sm font-semibold text-sand-800 dark:text-sand-200 hover:bg-sand-50 dark:hover:bg-sand-925 transition">
                                    <flux:icon name="eye" class="w-4 h-4" />
                                    <span>Detail</span>
                                </a>
                                <form action="{{ route('reports.destroy', $report) }}" method="POST" class="flex-1 md:flex-none flex"
                                      onsubmit="return confirm('Hapus laporan ini? Tindakan ini tidak dapat dibatalkan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus laporan ini secara permanen"
                                            class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold text-danger dark:text-danger-soft hover:bg-danger-tint dark:hover:bg-danger-deep/30 transition cursor-pointer">
                                        <flux:icon name="trash" class="w-4 h-4" />
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="block md:table-cell py-12 px-4 text-center text-sm text-sand-600 dark:text-sand-500">
                            Tidak ada laporan yang cocok dengan filter atau pencarian ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($reports->hasPages())
            <div class="p-4 border-t border-sand-200 dark:border-sand-900 text-sm">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
