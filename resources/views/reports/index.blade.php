@extends('layouts.app')

@section('title', 'SIRA — Sistem Informasi Ruang Aman')

@section('content')
<div class="space-y-6 sm:space-y-8 py-2 sm:py-4">
    <!-- Header Dasbor: judul, penjelasan singkat, dan aksi utama -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-5">
        <div class="space-y-1">
            <span class="text-xs uppercase tracking-wider text-emerald-700 dark:text-emerald-400 font-bold">Pengawasan Warga</span>
            <h1 class="font-sans text-[1.75rem] leading-[1.15] sm:text-4xl text-sand-975 dark:text-sand-100 font-extrabold tracking-tight">
                Daftar Laporan Fasilitas
            </h1>
            <p class="text-sm sm:text-base text-sand-600 dark:text-sand-400 leading-relaxed">
                Pantau perbaikan fasilitas publik dan dukung laporan warga di sekitar Anda.
            </p>
        </div>

        <!-- Aksi: lebar sama di ponsel, berdampingan di layar lebar -->
        <div class="grid grid-cols-2 sm:flex gap-3 shrink-0">
            <x-report-cta />
            <a href="{{ route('heatmap.index') }}"
               class="inline-flex items-center justify-center gap-1.5 px-3 sm:px-5 py-3 rounded-xl whitespace-nowrap border border-sand-200 dark:border-sand-850 bg-white dark:bg-sand-950 hover:bg-sand-50 dark:hover:bg-sand-925 text-sand-975 dark:text-sand-100 font-semibold text-sm transition">
                <flux:icon name="map" class="w-4 h-4 shrink-0 text-emerald-600 dark:text-emerald-400" />
                <span>Peta Sebaran</span>
            </a>
        </div>
    </div>

    <!-- Ringkasan status: satu permukaan, angka di depan label, tiap angka adalah filter -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-px rounded-2xl border border-sand-100 dark:border-sand-900 bg-sand-100 dark:bg-sand-900 shadow-2xs overflow-hidden">
        <a href="{{ route('reports.index') }}#dashboard" class="px-4 py-4 sm:px-6 sm:py-5 bg-white dark:bg-sand-950 hover:bg-sand-25 dark:hover:bg-sand-925 transition">
            <div class="text-2xl sm:text-3xl font-bold tabular-nums text-sand-975 dark:text-sand-100">{{ number_format($totalReports) }}</div>
            <div class="text-xs sm:text-sm text-sand-600 dark:text-sand-500 mt-1">Semua laporan</div>
        </a>
        <a href="{{ route('reports.index', ['rank_tier' => 'critical']) }}#dashboard" class="px-4 py-4 sm:px-6 sm:py-5 bg-white dark:bg-sand-950 hover:bg-sand-25 dark:hover:bg-sand-925 transition">
            <div class="text-2xl sm:text-3xl font-bold tabular-nums text-danger dark:text-danger-soft">{{ number_format($criticalCount) }}</div>
            <div class="text-xs sm:text-sm text-sand-600 dark:text-sand-500 mt-1">Prioritas kritis</div>
        </a>
        <a href="{{ route('reports.index', ['rank_tier' => 'urgent']) }}#dashboard" class="px-4 py-4 sm:px-6 sm:py-5 bg-white dark:bg-sand-950 hover:bg-sand-25 dark:hover:bg-sand-925 transition">
            <div class="text-2xl sm:text-3xl font-bold tabular-nums text-warning dark:text-warning-soft">{{ number_format($urgentCount) }}</div>
            <div class="text-xs sm:text-sm text-sand-600 dark:text-sand-500 mt-1">Prioritas mendesak</div>
        </a>
        <a href="{{ route('reports.index', ['status' => 'resolved']) }}#dashboard" class="px-4 py-4 sm:px-6 sm:py-5 bg-white dark:bg-sand-950 hover:bg-sand-25 dark:hover:bg-sand-925 transition">
            <div class="text-2xl sm:text-3xl font-bold tabular-nums text-emerald-700 dark:text-emerald-400">{{ number_format($resolvedCount) }}</div>
            <div class="text-xs sm:text-sm text-sand-600 dark:text-sand-500 mt-1">Sudah selesai</div>
        </a>
    </div>

    <!-- Feed Laporan & Filter yang Disederhanakan -->
    <x-dashboard
        :reports="$reports"
        :available-cities="is_iterable($availableCities) ? $availableCities : []"
        :available-districts="is_iterable($availableDistricts) ? $availableDistricts : []"
        :critical-reports="$criticalReports"
        :sort="$sort"
        :multi-issue-count="$multiIssueCount"
        :my-reports-count="$myReportsCount"
    />
</div>
@endsection
