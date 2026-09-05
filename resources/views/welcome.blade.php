@extends('layouts.app')

@section('title', 'SIRA &mdash; Sistem Informasi & Rekomendasi Aspirasi Publik')

@section('content')
<div class="space-y-16 py-4">
    <!-- Bagian Hero & Metrik Laporan -->
    <section class="border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] rounded-[8px] p-6 sm:p-12 relative overflow-hidden">
        <div class="max-w-4xl space-y-5">
            <div class="inline-flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#EDF3EC] text-[#346538] dark:bg-[#1C281E] dark:text-[#82C78A]">
                    Sistem Informasi &amp; Rekomendasi Aspirasi Publik
                </span>
                <span class="text-xs font-mono text-[#787774] dark:text-[#8E8D8A]">
                    Platform Terbuka Pengawasan Fasilitas Kota
                </span>
            </div>

            <h1 class="font-serif text-3xl sm:text-5xl lg:text-6xl text-[#111111] dark:text-[#EDEDEC] leading-[1.12] tracking-tight font-normal">
                Kawal fasilitas publik dengan bukti nyata dan suara warga.
            </h1>

            <p class="text-[#2F3437] dark:text-[#A1A09A] text-xs sm:text-base leading-relaxed max-w-3xl font-sans">
                SIRA adalah platform terbuka bagi masyarakat untuk mendokumentasikan jalan berlubang, saluran air tersumbat, lampu penerangan jalan mati, dan fasilitas terbengkalai. Dilengkapi pemetaan lokasi akurat, sistem voting dukungan warga, asisten cerdas <span class="font-mono text-emerald-600 dark:text-emerald-400 font-semibold">@Sira</span>, serta visualisasi peta sebaran masalah kota.
            </p>

            <!-- Tombol Aksi Cepat -->
            <div class="pt-2 flex flex-wrap items-center gap-3 font-mono text-xs">
                <a href="{{ route('reports.create') }}" class="px-4 py-2.5 bg-[#111111] hover:bg-[#2A2A2A] active:scale-[0.98] text-white dark:bg-[#EDEDEC] dark:text-[#111111] dark:hover:bg-white rounded-[6px] transition duration-150 font-medium inline-flex items-center gap-2">
                    <span>+ Buat Laporan Baru</span>
                </a>

                <a href="{{ route('reports.index') }}" class="px-4 py-2.5 bg-white dark:bg-[#181818] border border-[#EAEAEA] dark:border-[#282828] text-[#111111] dark:text-[#EDEDEC] hover:bg-[#F7F6F3] dark:hover:bg-[#202020] rounded-[6px] transition duration-150 font-medium inline-flex items-center gap-2">
                    <span>Lihat Semua Laporan</span>
                    <svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75">
                        <path d="M4 12L12 4M12 4H6M12 4V10" stroke-linecap="square"/>
                    </svg>
                </a>

                <a href="{{ route('heatmap.index') }}" class="px-4 py-2.5 bg-white dark:bg-[#181818] border border-[#EAEAEA] dark:border-[#282828] text-[#787774] dark:text-[#9B9B97] hover:text-[#111111] dark:hover:text-[#EDEDEC] hover:bg-[#F7F6F3] dark:hover:bg-[#202020] rounded-[6px] transition duration-150 font-medium inline-flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#9F2F2D]"></span>
                    <span>Peta Sebaran Masalah</span>
                </a>
            </div>
        </div>

        <!-- Metric Ledger Bar -->
        <div class="mt-10 pt-8 border-t border-[#EAEAEA] dark:border-[#222222] grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6 font-mono">
            <div class="p-3.5 rounded-[6px] bg-[#FBFBFA] dark:bg-[#181818] border border-[#EAEAEA] dark:border-[#262626]">
                <div class="text-[10px] uppercase text-[#787774] dark:text-[#8E8D8A] tracking-wider">Total Laporan Warga</div>
                <div class="text-xl sm:text-3xl font-bold text-[#111111] dark:text-[#EDEDEC] mt-0.5">
                    {{ number_format($totalReports) }}
                </div>
                <div class="text-[10px] text-[#787774] mt-0.5">Terdokumentasi</div>
            </div>

            <div class="p-3.5 rounded-[6px] bg-[#FBFBFA] dark:bg-[#181818] border border-[#EAEAEA] dark:border-[#262626]">
                <div class="text-[10px] uppercase text-[#9F2F2D] tracking-wider">Prioritas Kritis</div>
                <div class="text-xl sm:text-3xl font-bold text-[#9F2F2D] mt-0.5">
                    {{ number_format($criticalCount) }}
                </div>
                <div class="text-[10px] text-[#9F2F2D]/80 mt-0.5">Dukungan &ge; 100 Suara</div>
            </div>

            <div class="p-3.5 rounded-[6px] bg-[#FBFBFA] dark:bg-[#181818] border border-[#EAEAEA] dark:border-[#262626]">
                <div class="text-[10px] uppercase text-[#956400] dark:text-[#E0BE69] tracking-wider">Laporan Mendesak</div>
                <div class="text-xl sm:text-3xl font-bold text-[#956400] dark:text-[#E0BE69] mt-0.5">
                    {{ number_format($urgentCount) }}
                </div>
                <div class="text-[10px] text-[#787774] mt-0.5">Dukungan &ge; 50 Suara</div>
            </div>

            <div class="p-3.5 rounded-[6px] bg-[#FBFBFA] dark:bg-[#181818] border border-[#EAEAEA] dark:border-[#262626]">
                <div class="text-[10px] uppercase text-[#346538] dark:text-[#82C78A] tracking-wider">Tuntas / Selesai</div>
                <div class="text-xl sm:text-3xl font-bold text-[#346538] dark:text-[#82C78A] mt-0.5">
                    {{ number_format($resolvedCount) }}
                </div>
                <div class="text-[10px] text-[#346538]/80 dark:text-[#82C78A]/80 mt-0.5">Masalah Tertangani</div>
            </div>
        </div>
    </section>

    <!-- Bento Grid Fitur & Arsitektur Sistem SIRA -->
    <section id="fitur" class="space-y-6">
        <div>
            <span class="font-mono text-xs uppercase tracking-wider text-[#787774] dark:text-[#8E8D8A]">
                Fitur Utama
            </span>
            <h2 class="font-serif text-2xl sm:text-3xl text-[#111111] dark:text-[#EDEDEC] mt-1 font-normal">
                Empat pilar keterbukaan pengawasan fasilitas publik
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
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

                    <h3 class="font-serif text-xl text-[#111111] dark:text-[#EDEDEC] mb-2 font-normal">
                        Pemetaan &amp; Deteksi Lokasi Otomatis
                    </h3>

                    <p class="text-xs text-[#787774] dark:text-[#9B9B97] leading-[1.6] mb-4">
                        Cukup pilih titik koordinat langsung di peta terbuka. Nama jalan, rukun warga, kelurahan, dan kecamatan akan terdeteksi otomatis tanpa perlu diketik manual.
                    </p>

                    <div class="p-3 bg-[#FBFBFA] dark:bg-[#111111] border border-[#EAEAEA] dark:border-[#262626] rounded-[6px] font-mono text-[11px] leading-relaxed text-[#111111] dark:text-[#EDEDED] space-y-1">
                        <div class="text-[#787774]">📍 Contoh Wilayah Terdeteksi:</div>
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
                    <h4 class="font-serif text-lg text-[#111111] dark:text-[#EDEDEC] mb-2 font-normal">
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
                    <h4 class="font-serif text-lg text-[#111111] dark:text-[#EDEDEC] mb-2 font-normal">
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
                    <h4 class="font-serif text-lg text-[#111111] dark:text-[#EDEDEC] mb-2 font-normal">
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
    </section>

    <!-- Laporan Prioritas Tertinggi Saat Ini -->
    @if ($criticalReports->isNotEmpty())
        <section class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <span class="font-mono text-xs uppercase tracking-wider text-[#9F2F2D]">
                        Suara Terbanyak Warga
                    </span>
                    <h2 class="font-serif text-2xl sm:text-3xl text-[#111111] dark:text-[#EDEDEC] mt-1 font-normal">
                        Laporan prioritas paling mendesak saat ini
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

    <!-- Alur Kerja: 4 Langkah Partisipasi Warga -->
    <section id="cara-kerja" class="space-y-6">
        <div>
            <span class="font-mono text-xs uppercase tracking-wider text-[#787774] dark:text-[#8E8D8A]">
                Alur Partisipasi
            </span>
            <h2 class="font-serif text-2xl sm:text-3xl text-[#111111] dark:text-[#EDEDEC] mt-1 font-normal">
                Empat langkah mengawal perbaikan fasilitas publik
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="p-6 rounded-[8px] border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] space-y-3">
                <div class="font-mono text-xs text-[#787774] dark:text-[#8E8D8A]">Langkah 01</div>
                <h3 class="font-serif text-base text-[#111111] dark:text-[#EDEDEC] font-normal">
                    Foto Bukti Kerusakan
                </h3>
                <p class="text-xs text-[#787774] dark:text-[#9B9B97] leading-relaxed">
                    Ambil foto kondisi nyata fasilitas umum yang rusak di sekitar Anda sebagai bukti laporan yang valid dan tidak terbantahkan.
                </p>
            </div>

            <div class="p-6 rounded-[8px] border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] space-y-3">
                <div class="font-mono text-xs text-[#787774] dark:text-[#8E8D8A]">Langkah 02</div>
                <h3 class="font-serif text-base text-[#111111] dark:text-[#EDEDEC] font-normal">
                    Tandai Titik di Peta
                </h3>
                <p class="text-xs text-[#787774] dark:text-[#9B9B97] leading-relaxed">
                    Pilih titik lokasi pada peta terbuka. Alamat jalan, kelurahan, dan kecamatan akan terdata otomatis dengan presisi.
                </p>
            </div>

            <div class="p-6 rounded-[8px] border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] space-y-3">
                <div class="font-mono text-xs text-[#787774] dark:text-[#8E8D8A]">Langkah 03</div>
                <h3 class="font-serif text-base text-[#111111] dark:text-[#EDEDEC] font-normal">
                    Dukungan Warga &amp; Diskusi
                </h3>
                <p class="text-xs text-[#787774] dark:text-[#9B9B97] leading-relaxed">
                    Warga saling memberikan suara dukungan untuk menaikkan status menjadi Kritis, serta berdiskusi bersama asisten @Sira.
                </p>
            </div>

            <div class="p-6 rounded-[8px] border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] space-y-3">
                <div class="font-mono text-xs text-[#787774] dark:text-[#8E8D8A]">Langkah 04</div>
                <h3 class="font-serif text-base text-[#111111] dark:text-[#EDEDEC] font-normal">
                    Kawal Sampai Selesai
                </h3>
                <p class="text-xs text-[#787774] dark:text-[#9B9B97] leading-relaxed">
                    Pantau tindak lanjut perbaikan secara terbuka hingga pelapor mengonfirmasi bahwa fasilitas telah tuntas diperbaiki.
                </p>
            </div>
        </div>
    </section>

    <!-- Kategori Masalah Umum yang Dilaporkan -->
    <section class="p-6 sm:p-8 rounded-[8px] border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="font-mono text-[11px] uppercase tracking-wider text-[#787774]">Klasifikasi Masalah</span>
                <h3 class="font-serif text-xl text-[#111111] dark:text-[#EDEDEC] font-normal mt-0.5">
                    Fasilitas publik yang dapat dilaporkan
                </h3>
            </div>
            <a href="{{ route('reports.create') }}" class="text-xs font-mono text-[#111111] dark:text-[#EDEDEC] hover:underline underline-offset-2 shrink-0">
                + Laporkan Sekarang &rarr;
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 font-mono text-xs">
            <div class="p-3 rounded-[6px] border border-[#EAEAEA] dark:border-[#262626] bg-[#FBFBFA] dark:bg-[#181818] space-y-1">
                <span class="text-base">🕳️</span>
                <div class="font-semibold text-[#111111] dark:text-[#EDEDEC] text-[11px]">Jalan Berlubang</div>
                <p class="text-[10px] text-[#787774] font-sans">Aspal amblas &amp; retak</p>
            </div>

            <div class="p-3 rounded-[6px] border border-[#EAEAEA] dark:border-[#262626] bg-[#FBFBFA] dark:bg-[#181818] space-y-1">
                <span class="text-base">🌊</span>
                <div class="font-semibold text-[#111111] dark:text-[#EDEDEC] text-[11px]">Drainase &amp; Banjir</div>
                <p class="text-[10px] text-[#787774] font-sans">Saluran mampet &amp; genangan</p>
            </div>

            <div class="p-3 rounded-[6px] border border-[#EAEAEA] dark:border-[#262626] bg-[#FBFBFA] dark:bg-[#181818] space-y-1">
                <span class="text-base">💡</span>
                <div class="font-semibold text-[#111111] dark:text-[#EDEDEC] text-[11px]">Lampu Jalan Padam</div>
                <p class="text-[10px] text-[#787774] font-sans">Jalan gelap &amp; rawan begal</p>
            </div>

            <div class="p-3 rounded-[6px] border border-[#EAEAEA] dark:border-[#262626] bg-[#FBFBFA] dark:bg-[#181818] space-y-1">
                <span class="text-base">🗑️</span>
                <div class="font-semibold text-[#111111] dark:text-[#EDEDEC] text-[11px]">Sampah Liar</div>
                <p class="text-[10px] text-[#787774] font-sans">Tempat pembuangan liar</p>
            </div>

            <div class="p-3 rounded-[6px] border border-[#EAEAEA] dark:border-[#262626] bg-[#FBFBFA] dark:bg-[#181818] space-y-1">
                <span class="text-base">🚶</span>
                <div class="font-semibold text-[#111111] dark:text-[#EDEDEC] text-[11px]">Trotoar Rusak</div>
                <p class="text-[10px] text-[#787774] font-sans">Akses pejalan kaki</p>
            </div>

            <div class="p-3 rounded-[6px] border border-[#EAEAEA] dark:border-[#262626] bg-[#FBFBFA] dark:bg-[#181818] space-y-1">
                <span class="text-base">🚧</span>
                <div class="font-semibold text-[#111111] dark:text-[#EDEDEC] text-[11px]">Fasilitas Umum</div>
                <p class="text-[10px] text-[#787774] font-sans">Jembatan, halte, rambu</p>
            </div>
        </div>
    </section>

    <!-- Ajakan Bertindak (CTA Banner) -->
    <section class="border border-[#EAEAEA] dark:border-[#222222] bg-[#111111] dark:bg-[#161615] text-white p-8 sm:p-12 rounded-[8px] flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="space-y-2 max-w-xl text-center sm:text-left">
            <h3 class="font-serif text-2xl sm:text-3xl text-white font-normal leading-tight">
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
@endsection
