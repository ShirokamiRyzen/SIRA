@extends('layouts.app')

@section('title', 'Cara Kerja & Fitur Utama — SIRA')

@section('content')
<div class="space-y-16 py-2 sm:py-4">
    <!-- Gabungan: Cara Kerja & Fitur Utama Sistem SIRA -->
    <section id="cara-kerja-fitur" class="space-y-6 scroll-mt-20">
        <!-- Target Anchor ID untuk menjaga kompatibilitas hash URL #cara-kerja dan #fitur -->
        <div id="fitur" class="scroll-mt-24"></div>
        <div id="cara-kerja" class="scroll-mt-24"></div>

        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-[#EAEAEA] dark:border-[#222222] pb-5">
            <div>
                <div class="inline-flex items-center gap-2 mb-1.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#EDF3EC] text-[#346538] dark:bg-[#1C281E] dark:text-[#82C78A]">
                        Sistem Informasi Ruang Aman
                    </span>
                    <span class="font-mono text-xs uppercase tracking-wider text-[#787774] dark:text-[#8E8D8A]">
                        Panduan &amp; Kapabilitas
                    </span>
                </div>
                <h1 class="font-sans text-2xl sm:text-4xl text-[#111111] dark:text-[#EDEDEC] font-bold tracking-tight">
                    Cara Kerja &amp; Fitur Utama
                </h1>
                <p class="text-xs sm:text-sm text-[#787774] dark:text-[#9B9B97] font-sans mt-1 max-w-2xl">
                    Panduan interaktif partisipasi warga dan arsitektur kapabilitas sistem pengawasan fasilitas publik SIRA.
                </p>
            </div>

            <!-- Selector / Tab Switcher Interaktif (Perbaiki Selectable) -->
            <div class="inline-flex p-1 rounded-[8px] bg-[#F4F4F3] dark:bg-[#1A1A1A] border border-[#EAEAEA] dark:border-[#282828] text-xs font-mono select-none" role="tablist">
                <button type="button" onclick="switchGuideTab('all')" id="tab-btn-all"
                    class="guide-tab-btn px-3 py-1.5 rounded-[6px] transition font-medium cursor-pointer bg-white dark:bg-[#252525] text-[#111111] dark:text-[#EDEDEC] shadow-2xs">
                    Semua
                </button>
                <button type="button" onclick="switchGuideTab('cara-kerja')" id="tab-btn-cara-kerja"
                    class="guide-tab-btn px-3 py-1.5 rounded-[6px] transition font-medium cursor-pointer text-[#787774] dark:text-[#8E8D8A] hover:text-[#111111] dark:hover:text-[#EDEDEC]">
                    Cara Kerja
                </button>
                <button type="button" onclick="switchGuideTab('fitur')" id="tab-btn-fitur"
                    class="guide-tab-btn px-3 py-1.5 rounded-[6px] transition font-medium cursor-pointer text-[#787774] dark:text-[#8E8D8A] hover:text-[#111111] dark:hover:text-[#EDEDEC]">
                    Fitur Utama
                </button>
            </div>
        </div>

        <!-- Panel 1: Alur Kerja (4 Langkah Partisipasi Warga) -->
        <div id="panel-cara-kerja" class="space-y-4">
            <div class="flex items-center space-x-2 text-xs font-mono text-[#787774] dark:text-[#8E8D8A]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#111111] dark:bg-[#EDEDEC]"></span>
                <span class="uppercase tracking-wider">Alur Partisipasi (4 Langkah Pengawasan)</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 select-text">
                <div class="p-5 sm:p-6 rounded-[8px] border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] space-y-2.5">
                    <div class="font-mono text-xs text-[#787774] dark:text-[#8E8D8A]">Langkah 01</div>
                    <h3 class="font-sans text-base text-[#111111] dark:text-[#EDEDEC] font-semibold">
                        Foto Bukti Kerusakan
                    </h3>
                    <p class="text-xs text-[#787774] dark:text-[#9B9B97] leading-relaxed">
                        Ambil foto kondisi nyata fasilitas umum yang rusak di sekitar Anda sebagai bukti laporan yang valid dan tidak terbantahkan.
                    </p>
                </div>

                <div class="p-5 sm:p-6 rounded-[8px] border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] space-y-2.5">
                    <div class="font-mono text-xs text-[#787774] dark:text-[#8E8D8A]">Langkah 02</div>
                    <h3 class="font-sans text-base text-[#111111] dark:text-[#EDEDEC] font-semibold">
                        Tandai Titik di Peta
                    </h3>
                    <p class="text-xs text-[#787774] dark:text-[#9B9B97] leading-relaxed">
                        Pilih titik lokasi pada peta terbuka. Alamat jalan, kelurahan, dan kecamatan akan terdata otomatis dengan presisi.
                    </p>
                </div>

                <div class="p-5 sm:p-6 rounded-[8px] border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] space-y-2.5">
                    <div class="font-mono text-xs text-[#787774] dark:text-[#8E8D8A]">Langkah 03</div>
                    <h3 class="font-sans text-base text-[#111111] dark:text-[#EDEDEC] font-semibold">
                        Dukungan Warga &amp; Diskusi
                    </h3>
                    <p class="text-xs text-[#787774] dark:text-[#9B9B97] leading-relaxed">
                        Warga saling memberikan suara dukungan untuk menaikkan status menjadi Kritis, serta berdiskusi bersama asisten @Sira.
                    </p>
                </div>

                <div class="p-5 sm:p-6 rounded-[8px] border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] space-y-2.5">
                    <div class="font-mono text-xs text-[#787774] dark:text-[#8E8D8A]">Langkah 04</div>
                    <h3 class="font-sans text-base text-[#111111] dark:text-[#EDEDEC] font-semibold">
                        Kawal Sampai Selesai
                    </h3>
                    <p class="text-xs text-[#787774] dark:text-[#9B9B97] leading-relaxed">
                        Pantau tindak lanjut perbaikan secara terbuka hingga pelapor mengonfirmasi bahwa fasilitas telah tuntas diperbaiki.
                    </p>
                </div>
            </div>
        </div>

        <!-- Panel 2: Fitur Utama (Bento Grid Kapabilitas Platform) -->
        <div id="panel-fitur" class="space-y-4 pt-2">
            <div class="flex items-center space-x-2 text-xs font-mono text-[#787774] dark:text-[#8E8D8A]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#111111] dark:bg-[#EDEDEC]"></span>
                <span class="uppercase tracking-wider">Pilar Kapabilitas &amp; Arsitektur Sistem</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-5 select-text">
                <!-- Kotak 1: Telemetri Interaktif Livewire Volt -->
                <div class="md:col-span-7 flex flex-col">
                    <livewire:stack-status />
                </div>

                <!-- Kotak 2: Pemetaan & Deteksi Lokasi Otomatis -->
                <div class="md:col-span-5 border border-[#EAEAEA] dark:border-[#262626] rounded-[8px] bg-white dark:bg-[#161615] p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#E1F3FE] text-[#1F6C9F] dark:bg-[#142634] dark:text-[#76BBE8]">
                                Peta Interaktif
                            </span>
                            <span class="font-mono text-[11px] text-[#787774] dark:text-[#8E8D8A]">Bebas Akses</span>
                        </div>

                        <h3 class="font-sans text-xl text-[#111111] dark:text-[#EDEDEC] mb-2 font-bold">
                            Pemetaan &amp; Deteksi Lokasi Otomatis
                        </h3>

                        <p class="text-xs text-[#787774] dark:text-[#9B9B97] leading-[1.6] mb-4">
                            Cukup pilih titik koordinat langsung di peta terbuka. Nama jalan, rukun warga, kelurahan, dan kecamatan akan terdeteksi otomatis tanpa perlu diketik manual.
                        </p>

                        <div class="p-3 bg-[#FBFBFA] dark:bg-[#111111] border border-[#EAEAEA] dark:border-[#262626] rounded-[6px] font-mono text-[11px] leading-relaxed text-[#111111] dark:text-[#EDEDED] space-y-1">
                            <div class="text-[#787774] flex items-center gap-1.5">
                                <flux:icon name="map-pin" class="w-3.5 h-3.5 text-[#787774] shrink-0" />
                                <span>Contoh Wilayah Terdeteksi:</span>
                            </div>
                            <div class="text-[#1F6C9F] dark:text-[#76BBE8]">&bull; Jl. Kaliurang KM 5, Depok, Sleman</div>
                            <div class="text-[#787774]">&bull; Status: Titik Peta Tervalidasi</div>
                        </div>
                    </div>

                    <div class="pt-4 mt-4 border-t border-[#EAEAEA] dark:border-[#262626] flex items-center justify-between text-[11px] font-mono text-[#787774] dark:text-[#8E8D8A]">
                        <span>Titik Lokasi Akurat</span>
                        <a href="{{ route('heatmap.index') }}" class="hover:text-[#111111] dark:hover:text-[#EDEDED] underline underline-offset-2">Buka Peta &rarr;</a>
                    </div>
                </div>

                <!-- Kotak 3: Penentuan Prioritas Kolektif -->
                <div class="md:col-span-4 border border-[#EAEAEA] dark:border-[#262626] rounded-[8px] bg-white dark:bg-[#161615] p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#EDF3EC] text-[#346538] dark:bg-[#1C281E] dark:text-[#82C78A]">
                                Suara Warga
                            </span>
                            <span class="font-mono text-[11px] text-[#787774] dark:text-[#8E8D8A]">Demokratis</span>
                        </div>
                        <h4 class="font-sans text-lg text-[#111111] dark:text-[#EDEDEC] mb-2 font-semibold">
                            Penentuan Prioritas Kolektif
                        </h4>
                        <p class="text-xs text-[#787774] dark:text-[#9B9B97] leading-[1.6]">
                            Tingkat urgensi perbaikan tidak diputuskan sepihak. Semakin banyak warga sekitar yang memberikan dukungan (upvote), semakin tinggi status laporan menjadi Kritis.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#EAEAEA] dark:border-[#262626] text-[11px] font-mono text-[#787774] dark:text-[#8E8D8A]">
                        Aturan: 1 Akun = 1 Suara per Laporan
                    </div>
                </div>

                <!-- Kotak 4: Asisten Cerdas @Sira -->
                <div class="md:col-span-4 border border-[#EAEAEA] dark:border-[#262626] rounded-[8px] bg-white dark:bg-[#161615] p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#FBF3DB] text-[#956400] dark:bg-[#2F2917] dark:text-[#E0BE69]">
                                Bantuan Cerdas
                            </span>
                            <span class="font-mono text-[11px] text-[#787774] dark:text-[#8E8D8A]">Asisten @Sira</span>
                        </div>
                        <h4 class="font-sans text-lg text-[#111111] dark:text-[#EDEDEC] mb-2 font-semibold">
                            Analisis Masalah Otomatis
                        </h4>
                        <p class="text-xs text-[#787774] dark:text-[#9B9B97] leading-[1.6]">
                            Ketik tag @Sira pada komentar laporan. Asisten cerdas akan membantu mendiagnosa penyebab kerusakan fisik fasilitas dan merumuskan usulan solusi penanganan.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#EAEAEA] dark:border-[#262626] text-[11px] font-mono text-[#787774] dark:text-[#8E8D8A]">
                        Tersedia di setiap kolom komentar
                    </div>
                </div>

                <!-- Kotak 5: Peta Sebaran Titik Panas -->
                <div class="md:col-span-4 border border-[#EAEAEA] dark:border-[#262626] rounded-[8px] bg-white dark:bg-[#161615] p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#FDEBEC] text-[#9F2F2D] dark:bg-[#311617] dark:text-[#E88C8A]">
                                Peta Sebaran
                            </span>
                            <span class="font-mono text-[11px] text-[#787774] dark:text-[#8E8D8A]">Titik Panas</span>
                        </div>
                        <h4 class="font-sans text-lg text-[#111111] dark:text-[#EDEDEC] mb-2 font-semibold">
                            Pantau Titik Rawan Kota
                        </h4>
                        <p class="text-xs text-[#787774] dark:text-[#9B9B97] leading-[1.6]">
                            Peta sebaran panas memperlihatkan area kota yang memiliki konsentrasi kerusakan tertinggi, membantu pemetaan anggaran dan prioritas perbaikan infrastruktur.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#EAEAEA] dark:border-[#262626] text-[11px] font-mono text-[#787774] dark:text-[#8E8D8A]">
                        Pembaruan data secara berkala
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Laporan Sorotan Acak Komunitas -->
    @if ($criticalReports->isNotEmpty())
        <section class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <span class="font-mono text-xs uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                        Sorotan Komunitas
                    </span>
                    <h2 class="font-sans text-2xl sm:text-3xl text-[#111111] dark:text-[#EDEDEC] mt-1 font-bold">
                        Laporan fasilitas publik terkini
                    </h2>
                </div>
                <a href="{{ route('reports.index') }}" class="text-xs font-mono text-[#111111] dark:text-[#EDEDEC] hover:underline underline-offset-2 flex items-center gap-1.5 shrink-0">
                    <span>Lihat Seluruh Laporan ({{ number_format($totalReports) }})</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach ($criticalReports as $report)
                    <x-report-card :report="$report" />
                @endforeach
            </div>
        </section>
    @endif

    <!-- Kategori Masalah Umum yang Dilaporkan -->
    <section class="p-6 sm:p-8 rounded-[8px] border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="font-mono text-[11px] uppercase tracking-wider text-[#787774]">Klasifikasi Masalah</span>
                <h3 class="font-sans text-xl text-[#111111] dark:text-[#EDEDEC] font-bold mt-0.5">
                    Fasilitas publik yang dapat dilaporkan
                </h3>
            </div>
            <a href="{{ route('reports.create') }}" class="text-xs font-mono text-[#111111] dark:text-[#EDEDEC] hover:underline underline-offset-2 shrink-0">
                + Laporkan Sekarang &rarr;
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 font-mono text-xs">
            <div class="p-3 rounded-[6px] border border-[#EAEAEA] dark:border-[#262626] bg-[#FBFBFA] dark:bg-[#181818] space-y-1.5">
                <div class="w-7 h-7 rounded-[4px] bg-red-500/10 text-[#ef4444] flex items-center justify-center">
                    <flux:icon name="wrench" class="w-4 h-4" />
                </div>
                <div class="font-semibold text-[#111111] dark:text-[#EDEDEC] text-[11px]">Jalan Berlubang</div>
                <p class="text-[10px] text-[#787774] font-sans">Aspal amblas &amp; retak</p>
            </div>

            <div class="p-3 rounded-[6px] border border-[#EAEAEA] dark:border-[#262626] bg-[#FBFBFA] dark:bg-[#181818] space-y-1.5">
                <div class="w-7 h-7 rounded-[4px] bg-sky-500/10 text-[#0284c7] flex items-center justify-center">
                    <flux:icon name="cloud" class="w-4 h-4" />
                </div>
                <div class="font-semibold text-[#111111] dark:text-[#EDEDEC] text-[11px]">Drainase &amp; Banjir</div>
                <p class="text-[10px] text-[#787774] font-sans">Saluran mampet &amp; genangan</p>
            </div>

            <div class="p-3 rounded-[6px] border border-[#EAEAEA] dark:border-[#262626] bg-[#FBFBFA] dark:bg-[#181818] space-y-1.5">
                <div class="w-7 h-7 rounded-[4px] bg-amber-500/10 text-[#eab308] flex items-center justify-center">
                    <flux:icon name="light-bulb" class="w-4 h-4" />
                </div>
                <div class="font-semibold text-[#111111] dark:text-[#EDEDEC] text-[11px]">Lampu Jalan Padam</div>
                <p class="text-[10px] text-[#787774] font-sans">Jalan gelap &amp; rawan begal</p>
            </div>

            <div class="p-3 rounded-[6px] border border-[#EAEAEA] dark:border-[#262626] bg-[#FBFBFA] dark:bg-[#181818] space-y-1.5">
                <div class="w-7 h-7 rounded-[4px] bg-emerald-500/10 text-[#16a34a] flex items-center justify-center">
                    <flux:icon name="trash" class="w-4 h-4" />
                </div>
                <div class="font-semibold text-[#111111] dark:text-[#EDEDEC] text-[11px]">Sampah Liar</div>
                <p class="text-[10px] text-[#787774] font-sans">Tempat pembuangan liar</p>
            </div>

            <div class="p-3 rounded-[6px] border border-[#EAEAEA] dark:border-[#262626] bg-[#FBFBFA] dark:bg-[#181818] space-y-1.5">
                <div class="w-7 h-7 rounded-[4px] bg-orange-500/10 text-[#f97316] flex items-center justify-center">
                    <flux:icon name="user" class="w-4 h-4" />
                </div>
                <div class="font-semibold text-[#111111] dark:text-[#EDEDEC] text-[11px]">Trotoar Rusak</div>
                <p class="text-[10px] text-[#787774] font-sans">Akses pejalan kaki</p>
            </div>

            <div class="p-3 rounded-[6px] border border-[#EAEAEA] dark:border-[#262626] bg-[#FBFBFA] dark:bg-[#181818] space-y-1.5">
                <div class="w-7 h-7 rounded-[4px] bg-purple-500/10 text-[#8b5cf6] flex items-center justify-center">
                    <flux:icon name="shield-exclamation" class="w-4 h-4" />
                </div>
                <div class="font-semibold text-[#111111] dark:text-[#EDEDEC] text-[11px]">Fasilitas Umum</div>
                <p class="text-[10px] text-[#787774] font-sans">Jembatan, halte, rambu</p>
            </div>
        </div>
    </section>

    <!-- Ajakan Bertindak (CTA Banner) -->
    <section class="border border-[#EAEAEA] dark:border-[#222222] bg-[#111111] dark:bg-[#161615] text-white p-8 sm:p-12 rounded-[8px] flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="space-y-2 max-w-xl text-center sm:text-left">
            <h3 class="font-sans text-2xl sm:text-3xl text-white font-bold leading-tight">
                Mari bersama memperbaiki fasilitas publik di sekitar kita.
            </h3>
            <p class="text-xs sm:text-sm text-[#AAAAAA] leading-relaxed font-sans">
                Setiap laporan Anda adalah langkah awal menuju keterbukaan informasi dan respon penanganan fasilitas umum yang lebih cepat.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 font-mono text-xs shrink-0">
            <a href="{{ route('reports.create') }}" class="px-5 py-2.5 bg-white text-[#111111] hover:bg-[#EEEEEE] rounded-[6px] font-medium transition active:scale-[0.98]">
                + Buat Laporan Baru
            </a>
            <a href="{{ route('reports.index') }}" class="px-5 py-2.5 border border-[#444444] text-white hover:bg-white/10 rounded-[6px] transition active:scale-[0.98]">
                Buka Dasbor Laporan &rarr;
            </a>
        </div>
    </section>
</div>

@push('scripts')
<script>
    function switchGuideTab(tab) {
        const panelCaraKerja = document.getElementById('panel-cara-kerja');
        const panelFitur = document.getElementById('panel-fitur');
        const btnAll = document.getElementById('tab-btn-all');
        const btnCaraKerja = document.getElementById('tab-btn-cara-kerja');
        const btnFitur = document.getElementById('tab-btn-fitur');

        if (!panelCaraKerja || !panelFitur) return;

        const activeClass = 'guide-tab-btn px-3 py-1.5 rounded-[6px] transition font-medium cursor-pointer bg-white dark:bg-[#252525] text-[#111111] dark:text-[#EDEDEC] shadow-2xs';
        const inactiveClass = 'guide-tab-btn px-3 py-1.5 rounded-[6px] transition font-medium cursor-pointer text-[#787774] dark:text-[#8E8D8A] hover:text-[#111111] dark:hover:text-[#EDEDEC]';

        if (btnAll) btnAll.className = inactiveClass;
        if (btnCaraKerja) btnCaraKerja.className = inactiveClass;
        if (btnFitur) btnFitur.className = inactiveClass;

        if (tab === 'cara-kerja') {
            panelCaraKerja.classList.remove('hidden');
            panelFitur.classList.add('hidden');
            if (btnCaraKerja) btnCaraKerja.className = activeClass;
        } else if (tab === 'fitur') {
            panelCaraKerja.classList.add('hidden');
            panelFitur.classList.remove('hidden');
            if (btnFitur) btnFitur.className = activeClass;
        } else {
            panelCaraKerja.classList.remove('hidden');
            panelFitur.classList.remove('hidden');
            if (btnAll) btnAll.className = activeClass;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const hash = window.location.hash;
        if (hash === '#cara-kerja') {
            switchGuideTab('cara-kerja');
        } else if (hash === '#fitur') {
            switchGuideTab('fitur');
        }
    });

    window.addEventListener('hashchange', function () {
        const hash = window.location.hash;
        if (hash === '#cara-kerja') {
            switchGuideTab('cara-kerja');
        } else if (hash === '#fitur') {
            switchGuideTab('fitur');
        } else if (hash === '#cara-kerja-fitur') {
            switchGuideTab('all');
        }
    });
</script>
@endpush
@endsection
