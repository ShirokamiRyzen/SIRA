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

<div class="border border-[#EAEAEA] dark:border-[#262626] rounded-[8px] bg-white dark:bg-[#161615] overflow-hidden">
    <!-- Window Bar Header -->
    <div class="flex items-center justify-between px-4 py-3 border-b border-[#EAEAEA] dark:border-[#262626] bg-[#FAFAFA] dark:bg-[#141413]">
        <div class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-[#E0E0DE] dark:bg-[#333333] inline-block"></span>
            <span class="w-2.5 h-2.5 rounded-full bg-[#E0E0DE] dark:bg-[#333333] inline-block"></span>
            <span class="w-2.5 h-2.5 rounded-full bg-[#E0E0DE] dark:bg-[#333333] inline-block"></span>
            <span class="ml-2 font-mono text-[11px] text-[#787774] dark:text-[#8E8D8A]">panduan-sistem-sira</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-[0.05em] bg-[#EDF3EC] text-[#346538] dark:bg-[#1E2E20] dark:text-[#78C280]">
                Sistem Siap
            </span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-[0.05em] bg-[#E1F3FE] text-[#1F6C9F] dark:bg-[#172B38] dark:text-[#6CB9E8]">
                Akses Terbuka
            </span>
        </div>
    </div>

    <!-- Workspace Body -->
    <div class="p-6">
        <!-- Sub-Navigation / Tabs -->
        <div class="flex items-center gap-1 pb-3 mb-5 border-b border-[#EAEAEA] dark:border-[#262626]">
            <button 
                type="button" 
                wire:click="selectTab('ledger')"
                class="px-3 py-1 text-xs font-mono rounded-[4px] transition-colors {{ $activeTab === 'ledger' ? 'bg-[#111111] text-white dark:bg-[#EDEDED] dark:text-[#111111]' : 'text-[#787774] hover:text-[#111111] dark:text-[#8E8D8A] dark:hover:text-[#EDEDED]' }}">
                01. Alur Konsensus &amp; Vote
            </button>
            <button 
                type="button" 
                wire:click="selectTab('ai')"
                class="px-3 py-1 text-xs font-mono rounded-[4px] transition-colors {{ $activeTab === 'ai' ? 'bg-[#111111] text-white dark:bg-[#EDEDED] dark:text-[#111111]' : 'text-[#787774] hover:text-[#111111] dark:text-[#8E8D8A] dark:hover:text-[#EDEDED]' }}">
                02. Asisten Cerdas @Sira
            </button>
            <button 
                type="button" 
                wire:click="selectTab('geo')"
                class="px-3 py-1 text-xs font-mono rounded-[4px] transition-colors {{ $activeTab === 'geo' ? 'bg-[#111111] text-white dark:bg-[#EDEDED] dark:text-[#111111]' : 'text-[#787774] hover:text-[#111111] dark:text-[#8E8D8A] dark:hover:text-[#EDEDED]' }}">
                03. Pemetaan &amp; Lokasi
            </button>
        </div>

        @if ($activeTab === 'ledger')
            <div class="space-y-3 font-mono text-xs">
                <div class="flex items-center justify-between py-1.5 border-b border-[#EAEAEA]/60 dark:border-[#262626]">
                    <span class="text-[#787774] dark:text-[#8E8D8A]">Mekanisme Prioritas</span>
                    <span class="text-[#111111] dark:text-[#EDEDED]">Dukungan Suara (Vote) Warga</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-[#EAEAEA]/60 dark:border-[#262626]">
                    <span class="text-[#787774] dark:text-[#8E8D8A]">Tingkatan Masalah</span>
                    <span class="text-[#111111] dark:text-[#EDEDED]">Trending (10+) &rarr; Mendesak (50+) &rarr; Kritis (100+)</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-[#EAEAEA]/60 dark:border-[#262626]">
                    <span class="text-[#787774] dark:text-[#8E8D8A]">Hak Ubah Status Selesai</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] uppercase tracking-wider bg-[#EDF3EC] text-[#346538] dark:bg-[#1E2E20] dark:text-[#78C280]">
                        Khusus Pembuat Laporan
                    </span>
                </div>
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-[#787774] dark:text-[#8E8D8A]">Tahapan Penanganan</span>
                    <span class="text-[#111111] dark:text-[#EDEDED]">Aktif &bull; Sedang Diproses &bull; Selesai (Tuntas)</span>
                </div>
            </div>
        @elseif ($activeTab === 'ai')
            <div class="space-y-3 font-mono text-xs">
                <div class="flex items-center justify-between py-1.5 border-b border-[#EAEAEA]/60 dark:border-[#262626]">
                    <span class="text-[#787774] dark:text-[#8E8D8A]">Asisten Interaktif</span>
                    <span class="text-[#111111] dark:text-[#EDEDED]">Kecerdasan Buatan @Sira</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-[#EAEAEA]/60 dark:border-[#262626]">
                    <span class="text-[#787774] dark:text-[#8E8D8A]">Cara Memanggil</span>
                    <span class="text-[#111111] dark:text-[#EDEDED]">Sebut tag @Sira di kolom komentar</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-[#EAEAEA]/60 dark:border-[#262626]">
                    <span class="text-[#787774] dark:text-[#8E8D8A]">Fokus Analisis</span>
                    <span class="text-[#111111] dark:text-[#EDEDED]">Penyebab Kerusakan &amp; Solusi Teknis</span>
                </div>
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-[#787774] dark:text-[#8E8D8A]">Dukungan Format</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] uppercase tracking-wider bg-[#E1F3FE] text-[#1F6C9F] dark:bg-[#172B38] dark:text-[#6CB9E8]">
                        Markdown &amp; Rumus Formula
                    </span>
                </div>
            </div>
        @else
            <div class="space-y-3 font-mono text-xs">
                <div class="flex items-center justify-between py-1.5 border-b border-[#EAEAEA]/60 dark:border-[#262626]">
                    <span class="text-[#787774] dark:text-[#8E8D8A]">Peta Wilayah</span>
                    <span class="text-[#111111] dark:text-[#EDEDED]">Peta Terbuka Interaktif</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-[#EAEAEA]/60 dark:border-[#262626]">
                    <span class="text-[#787774] dark:text-[#8E8D8A]">Deteksi Alamat</span>
                    <span class="text-[#111111] dark:text-[#EDEDED]">Otomatis Mendeteksi Jalan, Desa, &amp; Kecamatan</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-[#EAEAEA]/60 dark:border-[#262626]">
                    <span class="text-[#787774] dark:text-[#8E8D8A]">Pemetaan Sebaran</span>
                    <span class="text-[#111111] dark:text-[#EDEDED]">Peta Panas (Heatmap) Titik Masalah</span>
                </div>
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-[#787774] dark:text-[#8E8D8A]">Ketepatan Lokasi</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] uppercase tracking-wider bg-[#FBF3DB] text-[#956400] dark:bg-[#2F2917] dark:text-[#E0BE69]">
                        Sesuai Titik Kerusakan Nyata
                    </span>
                </div>
            </div>
        @endif
    </div>

    <!-- Micro Footer Status bar -->
    <div class="px-6 py-2.5 bg-[#FBFBFA] dark:bg-[#141413] border-t border-[#EAEAEA] dark:border-[#262626] flex items-center justify-between text-[11px] font-mono text-[#787774] dark:text-[#8E8D8A]">
        <span>Status: Partisipasi warga berjalan aktif</span>
        <a href="{{ route('reports.index') }}" class="hover:text-[#111111] dark:hover:text-[#EDEDED] underline underline-offset-2">Buka Dasbor Laporan &rarr;</a>
    </div>
</div>
