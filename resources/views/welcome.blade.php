@extends('layouts.app')

@section('title', 'Cara Kerja & Fitur Utama — SIRA')

@section('content')
<div class="space-y-8 sm:space-y-12 py-2">
    <!-- ================================================================= -->
    <!-- HERO BANNER CAROUSEL ALA PORTAL RESMI PEMERINTAH (KOMDIGI STYLE)  -->
    <!-- ================================================================= -->
    <section class="relative rounded-2xl overflow-hidden shadow-xl border border-slate-200 dark:border-[#222222] bg-slate-950 text-white select-none" id="komdigi-hero-banner">
        @if ($criticalReports->isNotEmpty())
            <!-- Slide Track -->
            <div class="relative min-h-[390px] sm:min-h-[440px] md:min-h-[480px] lg:min-h-[520px] w-full overflow-hidden" id="gov-carousel-track">
                @foreach ($criticalReports as $index => $report)
                    <div class="gov-slide absolute inset-0 transition-opacity duration-500 ease-in-out flex flex-col justify-end p-4 sm:p-8 md:p-10 pb-16 sm:pb-8 {{ $index === 0 ? 'opacity-100 pointer-events-auto z-10' : 'opacity-0 pointer-events-none z-0' }}"
                         data-index="{{ $index }}">
                        <!-- Background Image dengan Aspect Fill & Dark Gradient Overlay -->
                        <div class="absolute inset-0 z-0">
                            <img src="{{ $report->image_base64 }}"
                                 alt="{{ $report->title }}"
                                 class="w-full h-full object-cover object-center filter brightness-[0.78] scale-100 transition-transform duration-700 ease-out">
                            <!-- Gradient Overlay Khas Portal Berita Pemerintah (Gelap di bawah & kiri) -->
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 to-slate-950/20"></div>
                            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/90 via-slate-950/45 to-transparent"></div>
                        </div>

                        <!-- Konten Slide (Teks, Badge, & Headline Besar) -->
                        <div class="relative z-10 max-w-3xl space-y-2.5 sm:space-y-4">
                            <!-- Baris Badge Kategori & Urgensi -->
                            <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                @if ($report->rank_tier === 'critical')
                                    <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-[4px] bg-[#E8590C] text-white text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider shadow-sm">
                                        LAPORAN KRITIS
                                    </span>
                                @elseif ($report->rank_tier === 'urgent')
                                    <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-[4px] bg-amber-500 text-slate-950 text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider shadow-sm">
                                        MENDESAK
                                    </span>
                                @else
                                    <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-[4px] bg-emerald-600 text-white text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider shadow-sm">
                                        SOROTAN WARGA
                                    </span>
                                @endif

                                <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-[4px] bg-white/20 backdrop-blur-xs text-white text-[10px] sm:text-[11px] font-mono border border-white/20">
                                    {{ $report->category_label }}
                                </span>

                                <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-[4px] bg-black/40 backdrop-blur-xs text-slate-300 text-[10px] sm:text-[11px] font-mono border border-white/10 hidden sm:inline-flex items-center gap-1">
                                    <flux:icon name="map-pin" class="w-3 h-3 text-emerald-400" />
                                    <span>{{ $report->district ?? $report->city ?? 'Lokasi Terdaftar' }}</span>
                                </span>
                            </div>

                            <!-- Judul Utama Besar (Komdigi Style) -->
                            <h2 class="text-lg sm:text-2xl md:text-3xl lg:text-4xl font-sans font-extrabold text-white leading-snug sm:leading-tight tracking-tight drop-shadow-md">
                                <a href="{{ route('reports.show', $report) }}" class="hover:text-emerald-300 transition line-clamp-2">
                                    {{ $report->title }}
                                </a>
                            </h2>

                            <!-- Deskripsi Singkat -->
                            <p class="text-xs sm:text-sm text-slate-200 line-clamp-2 max-w-2xl leading-relaxed drop-shadow-xs font-sans">
                                {{ Str::limit(strip_tags($report->description), 140) }}
                            </p>

                            <!-- Link Aksi: Baca Selengkapnya -->
                            <div class="pt-1 flex items-center gap-3">
                                <a href="{{ route('reports.show', $report) }}"
                                   class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 hover:bg-emerald-600 border border-white/20 hover:border-emerald-500 text-white font-semibold text-xs sm:text-sm transition-all group">
                                    <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center group-hover:bg-white group-hover:text-emerald-700 transition text-xs font-mono">&rarr;</span>
                                    <span>Baca Selengkapnya</span>
                                </a>

                                <span class="text-xs text-slate-300 font-mono hidden sm:inline">
                                    &bull; {{ $report->vote_score }} Dukungan Warga
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Indikator Angka Bulat Tunggal (1 2 3 4 5 ala Komdigi) di Pojok Kanan Bawah Banner -->
                <div class="absolute right-3 sm:right-8 bottom-3 sm:bottom-6 z-20 flex items-center space-x-1.5 sm:space-x-2">
                    <button type="button" id="gov-prev-btn"
                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-full font-mono text-xs font-bold transition flex items-center justify-center cursor-pointer bg-black/60 text-white/80 hover:bg-black/90 hover:text-white border border-white/20"
                        aria-label="Slide sebelumnya">
                        <flux:icon name="chevron-left" class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                    </button>
                    @foreach ($criticalReports as $numIndex => $rep)
                        <button type="button"
                            class="gov-num-pill w-7 h-7 sm:w-8 sm:h-8 rounded-full font-mono text-xs font-bold transition-all duration-200 flex items-center justify-center cursor-pointer {{ $numIndex === 0 ? 'bg-emerald-500 text-white font-black shadow-lg shadow-emerald-500/50 scale-110 ring-2 ring-white' : 'bg-black/60 text-white/75 hover:bg-black/90 hover:text-white border border-white/20' }}"
                            data-slide-index="{{ $numIndex }}"
                            aria-current="{{ $numIndex === 0 ? 'true' : 'false' }}"
                            aria-label="Pindah ke slide {{ $numIndex + 1 }}">
                            {{ $numIndex + 1 }}
                        </button>
                    @endforeach
                    <button type="button" id="gov-next-btn"
                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-full font-mono text-xs font-bold transition flex items-center justify-center cursor-pointer bg-black/60 text-white/80 hover:bg-black/90 hover:text-white border border-white/20"
                        aria-label="Slide berikutnya">
                        <flux:icon name="chevron-right" class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                    </button>
                </div>
            </div>

            <!-- Bagian Bawah: Strip Thumbnail "Laporan Populer" ala Komdigi -->
            <div class="relative z-20 bg-slate-950/85 backdrop-blur-md border-t border-white/10 px-3 sm:px-6 py-2.5 sm:py-3.5">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <div class="text-[10px] sm:text-[11px] uppercase tracking-wider font-mono text-slate-400 font-bold flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Laporan Populer &amp; Sorotan Warga</span>
                    </div>
                    <span class="text-[10px] font-mono text-slate-400 md:hidden flex items-center gap-1">
                        Geser &rarr;
                    </span>
                </div>

                <!-- Baris Thumbnail: Horizontal swipe scroll di mobile, 5 kolom di desktop -->
                <div class="relative flex overflow-x-auto md:grid md:grid-cols-5 gap-2 sm:gap-3 font-sans pb-1 px-0.5 snap-x snap-mandatory no-scrollbar" style="-webkit-overflow-scrolling: touch;">
                    @foreach ($criticalReports as $tIndex => $tReport)
                        <button type="button"
                            class="gov-thumb-card w-[210px] sm:w-[230px] md:w-auto shrink-0 md:shrink snap-start text-left p-1.5 sm:p-2 rounded-lg border transition cursor-pointer flex items-center gap-2 group {{ $tIndex === 0 ? 'border-emerald-500 bg-white/15 ring-1 ring-emerald-400/50' : 'border-white/10 bg-white/5 hover:bg-white/10 hover:border-white/20' }}"
                            data-thumb-index="{{ $tIndex }}">
                            <img src="{{ $tReport->image_base64 }}"
                                 alt="{{ $tReport->title }}"
                                 class="w-12 h-10 sm:w-14 sm:h-11 rounded object-cover shrink-0 border border-white/10">
                            <div class="min-w-0 flex-1">
                                <h4 class="text-[11px] sm:text-xs font-semibold text-white group-hover:text-emerald-300 transition truncate">
                                    {{ $tReport->title }}
                                </h4>
                                <div class="text-[10px] font-mono text-amber-400/90 truncate mt-0.5">
                                    {{ $tReport->category_label }} &bull; {{ $tReport->vote_score }} Suara
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Placeholder jika belum ada data laporan sama sekali -->
            <div class="p-12 text-center flex flex-col items-center justify-center space-y-4 font-mono text-xs">
                <p class="text-slate-400">Belum ada laporan aktif di sistem SIRA.</p>
                <a href="{{ route('reports.create') }}" class="px-5 py-2.5 rounded bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition">
                    + Buat Laporan Pertama
                </a>
            </div>
        @endif
    </section>

    <!-- ================================================================= -->
    <!-- BAGIAN 2: TOMBOL AKSI UTAMA & RINGKASAN DATA KOTA                 -->
    <!-- ================================================================= -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6 items-center">
        <!-- Tombol Aksi Cepat (Ramah Semua Kalangan) -->
        <div class="lg:col-span-6 flex flex-wrap items-center gap-3 font-mono text-xs">
            <a href="{{ route('reports.create') }}"
               class="px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white rounded-lg font-bold text-xs sm:text-sm inline-flex items-center gap-2 shadow-sm transition">
                <flux:icon name="plus-circle" class="w-4 h-4 text-white shrink-0" />
                <span>+ Laporkan Masalah</span>
            </a>
            <a href="{{ route('reports.index') }}"
               class="px-5 py-3.5 border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#161615] hover:bg-[#F7F6F3] dark:hover:bg-[#1E1E1E] text-[#111111] dark:text-[#EDEDEC] rounded-lg font-bold text-xs sm:text-sm inline-flex items-center gap-1.5 transition shadow-2xs">
                <span>Buka Dasbor Laporan &rarr;</span>
            </a>
        </div>

        <!-- 3 Kotak Indikator Angka Ringkas -->
        <div class="lg:col-span-6 grid grid-cols-3 gap-2.5 sm:gap-3 font-mono">
            <div class="p-3 sm:p-3.5 rounded-lg bg-white dark:bg-[#161615] border border-[#EAEAEA] dark:border-[#262626] shadow-2xs">
                <div class="text-[10px] uppercase text-[#787774] dark:text-[#8E8D8A] tracking-wider font-semibold">Total Laporan</div>
                <div class="text-lg sm:text-2xl font-bold text-[#111111] dark:text-[#EDEDEC] mt-0.5">
                    {{ number_format($totalReports) }}
                </div>
            </div>
            <div class="p-3 sm:p-3.5 rounded-lg bg-white dark:bg-[#161615] border border-[#EAEAEA] dark:border-[#262626] shadow-2xs">
                <div class="text-[10px] uppercase text-[#9F2F2D] tracking-wider font-semibold">Kritis / Mendesak</div>
                <div class="text-lg sm:text-2xl font-bold text-[#9F2F2D] mt-0.5">
                    {{ number_format($criticalCount + $urgentCount) }}
                </div>
            </div>
            <div class="p-3 sm:p-3.5 rounded-lg bg-white dark:bg-[#161615] border border-[#EAEAEA] dark:border-[#262626] shadow-2xs">
                <div class="text-[10px] uppercase text-emerald-700 dark:text-emerald-400 tracking-wider font-semibold">Tuntas Selesai</div>
                <div class="text-lg sm:text-2xl font-bold text-emerald-700 dark:text-emerald-400 mt-0.5">
                    {{ number_format($resolvedCount) }}
                </div>
            </div>
        </div>
    </section>

    <!-- ================================================================= -->
    <!-- BAGIAN 3: CARA KERJA SISTEM (3 LANGKAH MUDAH & BOOMER-PROOF)      -->
    <!-- ================================================================= -->
    <section id="cara-kerja-fitur" class="space-y-6 pt-2">
        <div id="cara-kerja"></div>
        <div id="fitur"></div>

        <div class="border-b border-[#EAEAEA] dark:border-[#222222] pb-3 flex flex-col sm:flex-row sm:items-end justify-between gap-2">
            <div>
                <span class="font-mono text-xs uppercase tracking-wider text-emerald-700 dark:text-emerald-400 font-bold">
                    Panduan Sederhana
                </span>
                <h3 class="font-sans text-2xl sm:text-3xl text-[#111111] dark:text-[#EDEDEC] font-bold tracking-tight mt-0.5">
                    Cara Kerja &amp; Fitur Utama SIRA
                </h3>
            </div>
            <p class="text-xs font-mono text-[#787774] dark:text-[#8E8D8A]">
                Cukup 3 langkah praktis dari ponsel Anda
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">
            <!-- Langkah 1 -->
            <div class="p-5 sm:p-6 rounded-xl border border-[#EAEAEA] dark:border-[#262626] bg-white dark:bg-[#161615] flex flex-col justify-between space-y-4 shadow-2xs">
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs font-mono shadow-xs">
                            01
                        </span>
                        <span class="text-[10px] font-mono uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded">
                            Langkah 1
                        </span>
                    </div>
                    <h4 class="font-sans text-base sm:text-lg text-[#111111] dark:text-[#EDEDEC] font-bold">
                        Foto Bukti Kerusakan
                    </h4>
                    <p class="text-xs sm:text-sm text-[#787774] dark:text-[#9B9B97] leading-relaxed font-sans">
                        Ambil foto kerusakan fasilitas di lokasi. Alamat jalan dan titik koordinat GPS akan terdeteksi otomatis dari kamera ponsel Anda.
                    </p>
                </div>
                <div class="pt-3 border-t border-[#EAEAEA] dark:border-[#262626] text-[11px] font-mono text-[#787774] flex items-center gap-1.5">
                    <flux:icon name="camera" class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                    <span>Otomatis via GPS HP</span>
                </div>
            </div>

            <!-- Langkah 2 -->
            <div class="p-5 sm:p-6 rounded-xl border border-[#EAEAEA] dark:border-[#262626] bg-white dark:bg-[#161615] flex flex-col justify-between space-y-4 shadow-2xs">
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="w-8 h-8 rounded-lg bg-sky-600 text-white flex items-center justify-center font-bold text-xs font-mono shadow-xs">
                            02
                        </span>
                        <span class="text-[10px] font-mono uppercase tracking-wider text-sky-700 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/50 px-2 py-0.5 rounded">
                            Langkah 2
                        </span>
                    </div>
                    <h4 class="font-sans text-base sm:text-lg text-[#111111] dark:text-[#EDEDEC] font-bold">
                        Dukungan Suara Warga
                    </h4>
                    <p class="text-xs sm:text-sm text-[#787774] dark:text-[#9B9B97] leading-relaxed font-sans">
                        Warga sekitar cukup menekan tombol Dukung (Upvote). Semakin banyak dukungan warga, laporan otomatis naik ke prioritas Kritis.
                    </p>
                </div>
                <div class="pt-3 border-t border-[#EAEAEA] dark:border-[#262626] text-[11px] font-mono text-[#787774] flex items-center gap-1.5">
                    <flux:icon name="hand-thumb-up" class="w-3.5 h-3.5 text-sky-600 shrink-0" />
                    <span>1 Akun = 1 Suara Sah</span>
                </div>
            </div>

            <!-- Langkah 3 -->
            <div class="p-5 sm:p-6 rounded-xl border border-[#EAEAEA] dark:border-[#262626] bg-white dark:bg-[#161615] flex flex-col justify-between space-y-4 shadow-2xs">
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs font-mono shadow-xs">
                            03
                        </span>
                        <span class="text-[10px] font-mono uppercase tracking-wider text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 px-2 py-0.5 rounded">
                            Langkah 3
                        </span>
                    </div>
                    <h4 class="font-sans text-base sm:text-lg text-[#111111] dark:text-[#EDEDEC] font-bold">
                        Respon Dinas &amp; Selesai
                    </h4>
                    <p class="text-xs sm:text-sm text-[#787774] dark:text-[#9B9B97] leading-relaxed font-sans">
                        Petugas dinas dan dinas terkait menindaklanjuti laporan. Perkembangan perbaikan dipantau terbuka hingga berstatus Selesai.
                    </p>
                </div>
                <div class="pt-3 border-t border-[#EAEAEA] dark:border-[#262626] text-[11px] font-mono text-[#787774] flex items-center gap-1.5">
                    <flux:icon name="check-circle" class="w-3.5 h-3.5 text-indigo-600 shrink-0" />
                    <span>Transparan &amp; Terbuka</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ================================================================= -->
    <!-- BAGIAN 4: AKSES CEPAT PETA SEBARAN & ASISTEN AI                   -->
    <!-- ================================================================= -->
    <section class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
        <!-- Peta Interaktif -->
        <div class="p-5 sm:p-6 rounded-xl border border-[#EAEAEA] dark:border-[#262626] bg-white dark:bg-[#161615] flex flex-col justify-between space-y-4 shadow-2xs">
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono tracking-wider uppercase bg-sky-50 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300 border border-sky-200 dark:border-sky-800/60 font-bold">
                        Visualisasi Wilayah
                    </span>
                    <span class="font-mono text-xs text-[#787774]">OpenFreeMap</span>
                </div>
                <h4 class="font-sans text-lg text-[#111111] dark:text-[#EDEDEC] font-bold">
                    Peta Sebaran Masalah Kota
                </h4>
                <p class="text-xs sm:text-sm text-[#787774] dark:text-[#9B9B97] leading-relaxed font-sans">
                    Lihat titik sebaran kerusakan jalan, lampu padam, dan fasilitas kota langsung di atas peta interaktif.
                </p>
            </div>
            <div class="pt-3 border-t border-[#EAEAEA] dark:border-[#262626] flex items-center justify-between">
                <span class="text-xs font-mono text-[#787774]">Akses Peta Gratis</span>
                <a href="{{ route('heatmap.index') }}" class="text-xs font-mono text-sky-600 dark:text-sky-400 hover:underline font-bold flex items-center gap-1">
                    <span>Buka Peta Sebaran</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        <!-- Asisten Cerdas AI @Sira -->
        <div class="p-5 sm:p-6 rounded-xl border border-[#EAEAEA] dark:border-[#262626] bg-white dark:bg-[#161615] flex flex-col justify-between space-y-4 shadow-2xs">
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono tracking-wider uppercase bg-amber-50 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 font-bold">
                        Bantuan Cerdas
                    </span>
                    <span class="font-mono text-xs text-[#787774]">Auto-Fallback</span>
                </div>
                <h4 class="font-sans text-lg text-[#111111] dark:text-[#EDEDEC] font-bold">
                    Asisten AI Cerdas @Sira
                </h4>
                <p class="text-xs sm:text-sm text-[#787774] dark:text-[#9B9B97] leading-relaxed font-sans">
                    Cukup ketik <code class="font-mono px-1.5 py-0.5 rounded bg-[#F4F4F3] dark:bg-[#202020] text-amber-800 dark:text-amber-300 font-bold">@Sira</code> pada kolom komentar untuk memperoleh analisa keparahan dan rekomendasi tindakan instan.
                </p>
            </div>
            <div class="pt-3 border-t border-[#EAEAEA] dark:border-[#262626] flex items-center justify-between">
                <span class="text-xs font-mono text-[#787774]">Siap di setiap laporan</span>
                <a href="{{ route('reports.index') }}" class="text-xs font-mono text-amber-700 dark:text-amber-400 hover:underline font-bold flex items-center gap-1">
                    <span>Lihat di Dasbor</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>
    </section>

    <!-- ================================================================= -->
    <!-- BAGIAN 5: BANNER AJAKAN LAPOR (AKHIR HALAMAN YANG BERSIH)         -->
    <!-- ================================================================= -->
    <section class="p-6 sm:p-8 rounded-xl border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
        <div class="space-y-1">
            <h4 class="font-sans text-lg sm:text-xl text-[#111111] dark:text-[#EDEDEC] font-bold tracking-tight">
                Temukan fasilitas umum yang rusak di sekitar Anda?
            </h4>
            <p class="text-xs sm:text-sm text-[#787774] dark:text-[#8E8D8A] font-sans">
                Setiap laporan warga mempercepat koordinasi penanganan dinas terkait.
            </p>
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('reports.create') }}" class="px-6 py-3 rounded-lg bg-[#111111] hover:bg-[#2A2A2A] text-white dark:bg-[#EDEDEC] dark:text-[#111111] text-xs font-mono font-bold transition shadow-xs">
                + Buat Laporan Sekarang
            </a>
        </div>
    </section>
</div>

@push('scripts')
<script>
    (function () {
        const slides = document.querySelectorAll('.gov-slide');
        const numPills = document.querySelectorAll('.gov-num-pill');
        const thumbCards = document.querySelectorAll('.gov-thumb-card');
        const heroBanner = document.getElementById('komdigi-hero-banner');

        if (slides.length <= 1) return;

        let currentIndex = 0;
        let slideTimer = null;

        function goToSlide(targetIndex) {
            if (targetIndex < 0) targetIndex = slides.length - 1;
            if (targetIndex >= slides.length) targetIndex = 0;
            currentIndex = targetIndex;

            // Transisi slide utama
            slides.forEach((slide, idx) => {
                if (idx === currentIndex) {
                    slide.classList.remove('opacity-0', 'pointer-events-none', 'z-0');
                    slide.classList.add('opacity-100', 'pointer-events-auto', 'z-10');
                } else {
                    slide.classList.remove('opacity-100', 'pointer-events-auto', 'z-10');
                    slide.classList.add('opacity-0', 'pointer-events-none', 'z-0');
                }
            });

            // Update status nomor lingkaran (1 2 3 4 5)
            numPills.forEach((pill) => {
                const pIdx = parseInt(pill.getAttribute('data-slide-index'), 10);
                if (pIdx === currentIndex) {
                    pill.className = 'gov-num-pill w-7 h-7 sm:w-8 sm:h-8 rounded-full font-mono text-xs font-bold transition-all duration-200 flex items-center justify-center cursor-pointer bg-emerald-500 text-white font-black shadow-lg shadow-emerald-500/50 scale-110 ring-2 ring-white';
                    pill.setAttribute('aria-current', 'true');
                } else {
                    pill.className = 'gov-num-pill w-7 h-7 sm:w-8 sm:h-8 rounded-full font-mono text-xs font-bold transition-all duration-200 flex items-center justify-center cursor-pointer bg-black/60 text-white/75 hover:bg-black/90 hover:text-white border border-white/20';
                    pill.setAttribute('aria-current', 'false');
                }
            });

            // Update status thumbnail baris bawah
            thumbCards.forEach((card, idx) => {
                if (idx === currentIndex) {
                    card.className = 'gov-thumb-card w-[210px] sm:w-[230px] md:w-auto shrink-0 md:shrink snap-start text-left p-1.5 sm:p-2 rounded-lg border transition cursor-pointer flex items-center gap-2 group border-emerald-500 bg-white/15 ring-1 ring-emerald-400/50';
                    const container = card.parentElement;
                    if (container && container.scrollWidth > container.clientWidth) {
                        const cardLeft = card.offsetLeft;
                        const cardWidth = card.offsetWidth;
                        const containerScroll = container.scrollLeft;
                        const containerWidth = container.clientWidth;

                        if (cardLeft < containerScroll) {
                            container.scrollTo({ left: Math.max(0, cardLeft - 8), behavior: 'smooth' });
                        } else if (cardLeft + cardWidth > containerScroll + containerWidth) {
                            container.scrollTo({ left: cardLeft + cardWidth - containerWidth + 8, behavior: 'smooth' });
                        }
                    }
                } else {
                    card.className = 'gov-thumb-card w-[210px] sm:w-[230px] md:w-auto shrink-0 md:shrink snap-start text-left p-1.5 sm:p-2 rounded-lg border transition cursor-pointer flex items-center gap-2 group border-white/10 bg-white/5 hover:bg-white/10 hover:border-white/20';
                }
            });
        }

        function startAutoPlay() {
            stopAutoPlay();
            slideTimer = setInterval(() => {
                goToSlide(currentIndex + 1);
            }, 5000);
        }

        function stopAutoPlay() {
            if (slideTimer) {
                clearInterval(slideTimer);
                slideTimer = null;
            }
        }

        // Listener klik nomor bulat (1 2 3 4 5)
        numPills.forEach((pill) => {
            pill.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const idx = parseInt(pill.getAttribute('data-slide-index'), 10);
                goToSlide(idx);
                startAutoPlay();
            });
        });

        // Listener tombol prev/next panah
        const prevBtn = document.getElementById('gov-prev-btn');
        const nextBtn = document.getElementById('gov-next-btn');
        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                goToSlide(currentIndex - 1);
                startAutoPlay();
            });
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                goToSlide(currentIndex + 1);
                startAutoPlay();
            });
        }

        // Listener klik thumbnail card bawah
        thumbCards.forEach((card) => {
            card.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const idx = parseInt(card.getAttribute('data-thumb-index'), 10);
                goToSlide(idx);
                startAutoPlay();
            });
        });

        // Pause autoplay saat mouse diarahkan ke hero banner
        if (heroBanner) {
            heroBanner.addEventListener('mouseenter', stopAutoPlay);
            heroBanner.addEventListener('mouseleave', startAutoPlay);

            // Gesture swipe layar sentuh di ponsel
            let touchStartX = 0;
            let touchEndX = 0;

            heroBanner.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            heroBanner.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                const deltaX = touchEndX - touchStartX;
                if (Math.abs(deltaX) > 40) {
                    if (deltaX < 0) {
                        goToSlide(currentIndex + 1);
                    } else {
                        goToSlide(currentIndex - 1);
                    }
                    startAutoPlay();
                }
            }, { passive: true });
        }

        goToSlide(0);
        startAutoPlay();
    })();
</script>
@endpush
@endsection
