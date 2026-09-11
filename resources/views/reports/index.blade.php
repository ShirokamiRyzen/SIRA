@extends('layouts.app')

@section('title', 'SIRA — Sistem Informasi Ruang Aman')

@section('content')
<div class="space-y-6 sm:space-y-8 py-2">
    <!-- Header Dasbor Ringkas & Boomer-Proof -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[#EAEAEA] dark:border-[#222222] pb-5">
        <div>
            <div class="inline-flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 font-semibold">
                    Pengawasan Warga
                </span>
                <span class="font-mono text-xs text-[#787774] dark:text-[#8E8D8A]">
                    Sistem Terbuka
                </span>
            </div>
            <h1 class="font-sans text-2xl sm:text-3xl text-[#111111] dark:text-[#EDEDEC] font-bold tracking-tight">
                Daftar Laporan Fasilitas
            </h1>
            <p class="text-xs sm:text-sm text-[#787774] dark:text-[#9B9B97] font-sans mt-0.5">
                Pantau perkembangan perbaikan fasilitas publik atau berikan dukungan suara Anda.
            </p>
        </div>

        <div class="flex items-center gap-2.5 sm:gap-3 shrink-0 flex-wrap">
            @auth
                <a href="{{ request('my_reports') ? route('reports.index') : route('reports.index', ['my_reports' => 1]) }}#dashboard"
                   class="px-3.5 py-2.5 border {{ request('my_reports') ? 'bg-emerald-600 border-emerald-600 text-white font-bold' : 'border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#161615] hover:bg-[#F7F6F3] dark:hover:bg-[#1E1E1E] text-[#111111] dark:text-[#EDEDEC]' }} text-xs font-mono font-medium rounded-[6px] transition inline-flex items-center gap-1.5 shadow-2xs">
                    <flux:icon name="user" class="w-3.5 h-3.5 {{ request('my_reports') ? 'text-white' : 'text-emerald-600 dark:text-emerald-400' }}" />
                    <span>Laporan Saya ({{ $myReportsCount }})</span>
                </a>
            @endauth
            <a href="{{ route('reports.create') }}" class="px-5 py-2.5 bg-[#111111] hover:bg-[#2A2A2A] active:scale-[0.98] text-white dark:bg-[#EDEDEC] dark:text-[#111111] dark:hover:bg-white text-xs font-mono font-medium rounded-[6px] transition inline-flex items-center gap-1.5 shadow-xs">
                <span>+ Buat Laporan Baru</span>
            </a>
            <a href="{{ route('heatmap.index') }}" class="px-4 py-2.5 border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#161615] hover:bg-[#F7F6F3] dark:hover:bg-[#1E1E1E] text-[#111111] dark:text-[#EDEDEC] text-xs font-mono font-medium rounded-[6px] transition inline-flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#9F2F2D]"></span>
                <span>Peta Sebaran</span>
            </a>
        </div>
    </div>

    <!-- Ringkasan Angka Status (4 Kotak Rapi & Kontras Tinggi) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 font-mono">
        <a href="{{ route('reports.index') }}#dashboard"
            class="p-3 sm:p-3.5 rounded-[8px] border border-[#EAEAEA] dark:border-[#262626] bg-white dark:bg-[#161615] hover:border-[#BBBBBB] dark:hover:border-[#444444] transition flex flex-col justify-between shadow-xs">
            <div class="text-[10px] sm:text-[11px] uppercase tracking-wider text-[#787774] dark:text-[#8E8D8A]">Semua Laporan</div>
            <div class="text-lg sm:text-2xl font-bold text-[#111111] dark:text-[#EDEDEC] mt-1">{{ number_format($totalReports) }}</div>
        </a>
        <a href="{{ route('reports.index', ['rank_tier' => 'critical']) }}#dashboard"
            class="p-3 sm:p-3.5 rounded-[8px] border border-[#EAEAEA] dark:border-[#262626] bg-white dark:bg-[#161615] hover:border-[#9F2F2D]/50 transition flex flex-col justify-between shadow-xs">
            <div class="text-[10px] sm:text-[11px] uppercase tracking-wider text-[#9F2F2D]">Prioritas Kritis</div>
            <div class="text-lg sm:text-2xl font-bold text-[#9F2F2D] mt-1">{{ number_format($criticalCount) }}</div>
        </a>
        <a href="{{ route('reports.index', ['rank_tier' => 'urgent']) }}#dashboard"
            class="p-3 sm:p-3.5 rounded-[8px] border border-[#EAEAEA] dark:border-[#262626] bg-white dark:bg-[#161615] hover:border-[#956400]/50 transition flex flex-col justify-between shadow-xs">
            <div class="text-[10px] sm:text-[11px] uppercase tracking-wider text-[#956400] dark:text-[#E0BE69]">Prioritas Mendesak</div>
            <div class="text-lg sm:text-2xl font-bold text-[#956400] dark:text-[#E0BE69] mt-1">{{ number_format($urgentCount) }}</div>
        </a>
        <a href="{{ route('reports.index', ['status' => 'resolved']) }}#dashboard"
            class="p-3 sm:p-3.5 rounded-[8px] border border-[#EAEAEA] dark:border-[#262626] bg-white dark:bg-[#161615] hover:border-emerald-500/50 transition flex flex-col justify-between shadow-xs">
            <div class="text-[10px] sm:text-[11px] uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Tuntas Selesai</div>
            <div class="text-lg sm:text-2xl font-bold text-emerald-700 dark:text-emerald-400 mt-1">{{ number_format($resolvedCount) }}</div>
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
