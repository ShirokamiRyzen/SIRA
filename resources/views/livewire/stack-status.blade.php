<?php

use function Livewire\Volt\{state};

state([
    'activeTab' => 'ledger',
    'copiedCommand' => null,
]);

$selectTab = function (string $tab) {
    $this->activeTab = $tab;
};

$copyCommand = function (string $key) {
    $this->copiedCommand = $key;
};

?>

<div class="border border-sand-100 dark:border-sand-900 rounded-xl bg-white dark:bg-sand-950 overflow-hidden">
    <!-- Window Bar Header -->
    <div class="flex items-center justify-between px-4 py-3 border-b border-sand-100 dark:border-sand-900 bg-sand-25 dark:bg-sand-975">
        <div class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-sand-200 dark:bg-sand-800 inline-block"></span>
            <span class="w-2.5 h-2.5 rounded-full bg-sand-200 dark:bg-sand-800 inline-block"></span>
            <span class="w-2.5 h-2.5 rounded-full bg-sand-200 dark:bg-sand-800 inline-block"></span>
            <span class="ml-2 font-mono text-[11px] text-sand-600 dark:text-sand-500 truncate max-w-[130px] sm:max-w-none">panduan-sistem-sira</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-[0.05em] bg-success-tint text-success dark:bg-success-deep dark:text-success-soft">
                Sistem Siap
            </span>
            <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-[0.05em] bg-info-tint text-info dark:bg-info-deep dark:text-info-soft">
                Akses Terbuka
            </span>
        </div>
    </div>

    <!-- Workspace Body -->
    <div class="p-4 sm:p-6">
        <!-- Sub-Navigation / Tabs -->
        <div class="flex items-center gap-1 pb-3 mb-5 border-b border-sand-100 dark:border-sand-900 overflow-x-auto">
            <button 
                type="button" 
                wire:click="selectTab('ledger')"
                class="px-3 py-1 text-xs font-mono rounded-md whitespace-nowrap shrink-0 transition-colors {{ $activeTab === 'ledger' ? 'bg-emerald-600 text-white' : 'text-sand-600 hover:text-sand-975 dark:text-sand-500 dark:hover:text-sand-100' }}">
                01. Alur Konsensus &amp; Vote
            </button>
            <button 
                type="button" 
                wire:click="selectTab('ai')"
                class="px-3 py-1 text-xs font-mono rounded-md whitespace-nowrap shrink-0 transition-colors {{ $activeTab === 'ai' ? 'bg-emerald-600 text-white' : 'text-sand-600 hover:text-sand-975 dark:text-sand-500 dark:hover:text-sand-100' }}">
                02. Asisten Cerdas @Sira
            </button>
            <button 
                type="button" 
                wire:click="selectTab('geo')"
                class="px-3 py-1 text-xs font-mono rounded-md whitespace-nowrap shrink-0 transition-colors {{ $activeTab === 'geo' ? 'bg-emerald-600 text-white' : 'text-sand-600 hover:text-sand-975 dark:text-sand-500 dark:hover:text-sand-100' }}">
                03. Pemetaan &amp; Lokasi
            </button>
        </div>

        @if ($activeTab === 'ledger')
            <div class="space-y-3 font-mono text-xs">
                <div class="flex items-center justify-between py-1.5 border-b border-sand-100/60 dark:border-sand-900">
                    <span class="text-sand-600 dark:text-sand-500">Mekanisme Prioritas</span>
                    <span class="text-sand-975 dark:text-sand-100">Dukungan Suara (Vote) Warga</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-sand-100/60 dark:border-sand-900">
                    <span class="text-sand-600 dark:text-sand-500">Tingkatan Masalah</span>
                    <span class="text-sand-975 dark:text-sand-100">Trending (10+) &rarr; Mendesak (50+) &rarr; Kritis (100+)</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-sand-100/60 dark:border-sand-900">
                    <span class="text-sand-600 dark:text-sand-500">Hak Ubah Status Selesai</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] uppercase tracking-wider bg-success-tint text-success dark:bg-success-deep dark:text-success-soft">
                        Khusus Pembuat Laporan
                    </span>
                </div>
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-sand-600 dark:text-sand-500">Tahapan Penanganan</span>
                    <span class="text-sand-975 dark:text-sand-100">Aktif &bull; Sedang Diproses &bull; Selesai (Tuntas)</span>
                </div>
            </div>
        @elseif ($activeTab === 'ai')
            <div class="space-y-3 font-mono text-xs">
                <div class="flex items-center justify-between py-1.5 border-b border-sand-100/60 dark:border-sand-900">
                    <span class="text-sand-600 dark:text-sand-500">Asisten Interaktif</span>
                    <span class="text-sand-975 dark:text-sand-100">Kecerdasan Buatan @Sira</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-sand-100/60 dark:border-sand-900">
                    <span class="text-sand-600 dark:text-sand-500">Cara Memanggil</span>
                    <span class="text-sand-975 dark:text-sand-100">Sebut tag @Sira di kolom komentar</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-sand-100/60 dark:border-sand-900">
                    <span class="text-sand-600 dark:text-sand-500">Fokus Analisis</span>
                    <span class="text-sand-975 dark:text-sand-100">Penyebab Kerusakan &amp; Solusi Teknis</span>
                </div>
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-sand-600 dark:text-sand-500">Dukungan Format</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] uppercase tracking-wider bg-info-tint text-info dark:bg-info-deep dark:text-info-soft">
                        Markdown &amp; Rumus Formula
                    </span>
                </div>
            </div>
        @else
            <div class="space-y-3 font-mono text-xs">
                <div class="flex items-center justify-between py-1.5 border-b border-sand-100/60 dark:border-sand-900">
                    <span class="text-sand-600 dark:text-sand-500">Peta Wilayah</span>
                    <span class="text-sand-975 dark:text-sand-100">Peta Terbuka Interaktif</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-sand-100/60 dark:border-sand-900">
                    <span class="text-sand-600 dark:text-sand-500">Deteksi Alamat</span>
                    <span class="text-sand-975 dark:text-sand-100">Otomatis Mendeteksi Jalan, Desa, &amp; Kecamatan</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-sand-100/60 dark:border-sand-900">
                    <span class="text-sand-600 dark:text-sand-500">Pemetaan Sebaran</span>
                    <span class="text-sand-975 dark:text-sand-100">Peta Panas (Heatmap) Titik Masalah</span>
                </div>
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-sand-600 dark:text-sand-500">Ketepatan Lokasi</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] uppercase tracking-wider bg-warning-tint text-warning dark:bg-warning-deep dark:text-warning-soft">
                        Sesuai Titik Kerusakan Nyata
                    </span>
                </div>
            </div>
        @endif
    </div>

    <!-- Micro Footer Status bar -->
    <div class="px-6 py-2.5 bg-sand-25 dark:bg-sand-975 border-t border-sand-100 dark:border-sand-900 flex items-center justify-between text-[11px] font-mono text-sand-600 dark:text-sand-500">
        <span>Status: Partisipasi warga berjalan aktif</span>
        <a href="{{ route('reports.index') }}" class="hover:text-sand-975 dark:hover:text-sand-100 underline underline-offset-2">Buka Dasbor Laporan &rarr;</a>
    </div>
</div>
