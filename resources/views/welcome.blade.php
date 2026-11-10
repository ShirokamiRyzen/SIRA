@extends('layouts.app')

@section('title', 'Cara Kerja & Fitur Utama — SIRA')

@section('content')
<div class="space-y-12 sm:space-y-16 py-2 sm:py-4">
    <!-- ================================================================= -->
    <!-- BAGIAN 1: PEMBUKA — APA ITU SIRA, AKSI UTAMA, & RINGKASAN DATA    -->
    <!-- ================================================================= -->
    <section class="space-y-6">
        <div class="space-y-3 max-w-2xl">
            <h1 class="font-sans text-[1.75rem] leading-[1.15] sm:text-4xl md:text-5xl font-extrabold tracking-tight text-sand-975 dark:text-sand-100">
                Fasilitas umum rusak di sekitar Anda?
            </h1>
            <p class="text-sm sm:text-base text-sand-600 dark:text-sand-400 leading-relaxed">
                Laporkan lewat foto, ajak warga sekitar ikut mendukung, dan pantau bersama sampai diperbaiki.
            </p>
        </div>

        <!-- Aksi utama: lebar sama di ponsel, berdampingan di layar lebar -->
        <div class="grid grid-cols-2 sm:flex gap-3">
            <x-report-cta />
            <a href="{{ route('reports.index') }}"
               class="inline-flex items-center justify-center gap-1.5 px-3 sm:px-5 py-3 rounded-xl whitespace-nowrap border border-sand-200 dark:border-sand-850 bg-white dark:bg-sand-950 hover:bg-sand-50 dark:hover:bg-sand-925 text-sand-975 dark:text-sand-100 font-semibold text-sm transition">
                <span>Dasbor Laporan</span>
                <span aria-hidden="true">&rarr;</span>
            </a>
        </div>

        <!-- Ringkasan data: satu permukaan, angka di depan label -->
        <div class="grid grid-cols-3 divide-x divide-sand-100 dark:divide-sand-900 rounded-2xl border border-sand-100 dark:border-sand-900 bg-white dark:bg-sand-950 shadow-2xs">
            <div class="px-3 py-4 sm:px-6 sm:py-5">
                <div class="text-2xl sm:text-3xl font-bold tabular-nums text-sand-975 dark:text-sand-100">{{ number_format($totalReports) }}</div>
                <div class="text-xs sm:text-sm text-sand-600 dark:text-sand-500 mt-1">Total laporan</div>
            </div>
            <div class="px-3 py-4 sm:px-6 sm:py-5">
                <div class="text-2xl sm:text-3xl font-bold tabular-nums text-danger dark:text-danger-soft">{{ number_format($criticalCount + $urgentCount) }}</div>
                <div class="text-xs sm:text-sm text-sand-600 dark:text-sand-500 mt-1">Kritis &amp; mendesak</div>
            </div>
            <div class="px-3 py-4 sm:px-6 sm:py-5">
                <div class="text-2xl sm:text-3xl font-bold tabular-nums text-emerald-700 dark:text-emerald-400">{{ number_format($resolvedCount) }}</div>
                <div class="text-xs sm:text-sm text-sand-600 dark:text-sand-500 mt-1">Sudah selesai</div>
            </div>
        </div>
    </section>

    <!-- ================================================================= -->
    <!-- BAGIAN 2: SOROTAN WARGA (CAROUSEL LAPORAN POPULER)                -->
    <!-- ================================================================= -->
    <div class="space-y-4">
    <div class="flex items-end justify-between gap-3">
        <div>
            <span class="text-xs uppercase tracking-wider text-emerald-700 dark:text-emerald-400 font-bold">Sorotan Warga</span>
            <h2 class="font-sans text-xl sm:text-2xl text-sand-975 dark:text-sand-100 font-bold tracking-tight mt-0.5">Laporan yang paling didukung</h2>
        </div>
        <a href="{{ route('reports.index') }}" class="shrink-0 text-sm font-semibold text-emerald-700 dark:text-emerald-400 hover:underline">Lihat semua &rarr;</a>
    </div>
    <section class="relative rounded-2xl overflow-hidden shadow-md border border-slate-200 dark:border-sand-900 bg-slate-950 text-white select-none" id="komdigi-hero-banner">
        @if ($criticalReports->isNotEmpty())
            <!-- Slide Track -->
            <div class="relative min-h-[460px] sm:min-h-[520px] md:min-h-[560px] lg:min-h-[600px] w-full overflow-hidden" id="gov-carousel-track">
                @foreach ($criticalReports as $index => $report)
                    <div class="gov-slide absolute inset-0 transition-opacity duration-500 ease-in-out flex flex-col justify-end p-4 sm:p-8 md:p-10 pb-16 sm:pb-8 {{ $index === 0 ? 'opacity-100 pointer-events-auto z-10' : 'opacity-0 pointer-events-none z-0' }}"
                         data-index="{{ $index }}">
                        <!-- Background Image dengan Aspect Fill & Dark Gradient Overlay -->
                        <div class="absolute inset-0 z-0">
                            <img src="{{ $report->image_base64 }}"
                                 alt="{{ $report->title }}"
                                 class="w-full h-full object-cover object-center scale-100 transition-transform duration-700 ease-out">
                            <!-- Gradasi hanya di bagian bawah, cukup untuk keterbacaan teks; foto tetap jelas -->
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/50 via-35% to-transparent to-70%"></div>
                            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/50 to-transparent to-60%"></div>
                        </div>

                        <!-- Konten Slide (Teks, Badge, & Headline Besar) -->
                        <div class="relative z-10 max-w-3xl space-y-2.5 sm:space-y-4">
                            <!-- Baris Badge Kategori & Urgensi -->
                            <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                @if ($report->rank_tier === 'critical')
                                    <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-md bg-accent text-white text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider shadow-sm">
                                        LAPORAN KRITIS
                                    </span>
                                @elseif ($report->rank_tier === 'urgent')
                                    <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-md bg-amber-500 text-slate-950 text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider shadow-sm">
                                        MENDESAK
                                    </span>
                                @else
                                    <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-md bg-emerald-600 text-white text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider shadow-sm">
                                        SOROTAN WARGA
                                    </span>
                                @endif

                                <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-md bg-white/20 backdrop-blur-xs text-white text-[10px] sm:text-[11px] font-mono border border-white/20">
                                    {{ $report->category_label }}
                                </span>

                                <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-md bg-black/40 backdrop-blur-xs text-slate-300 text-[10px] sm:text-[11px] font-mono border border-white/10 hidden sm:inline-flex items-center gap-1">
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
                <x-report-cta label="Buat Laporan Pertama" />
            </div>
        @endif
    </section>
    </div>

    <!-- ================================================================= -->
    <!-- BAGIAN 3: CARA KERJA SISTEM (3 LANGKAH MUDAH & BOOMER-PROOF)      -->
    <!-- ================================================================= -->
    <section id="cara-kerja-fitur" class="space-y-4">
        <div id="cara-kerja"></div>
        <div id="fitur"></div>

        <div>
            <span class="text-xs uppercase tracking-wider text-emerald-700 dark:text-emerald-400 font-bold">
                Panduan Sederhana
            </span>
            <h2 class="font-sans text-xl sm:text-2xl text-sand-975 dark:text-sand-100 font-bold tracking-tight mt-0.5">
                Cara Kerja &amp; Fitur Utama SIRA
            </h2>
            <p class="text-sm text-sand-600 dark:text-sand-500 mt-1">
                Cukup 3 langkah praktis dari ponsel Anda.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">
            <!-- Langkah 1 -->
            <div class="p-5 sm:p-6 rounded-xl border border-sand-100 dark:border-sand-900 bg-white dark:bg-sand-950 flex flex-col justify-between space-y-4 shadow-2xs">
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs font-mono shadow-xs">
                            01
                        </span>
                    </div>
                    <h4 class="font-sans text-base sm:text-lg text-sand-975 dark:text-sand-100 font-bold">
                        Foto Bukti Kerusakan
                    </h4>
                    <p class="text-xs sm:text-sm text-sand-600 dark:text-sand-500 leading-relaxed font-sans">
                        Ambil foto kerusakan fasilitas di lokasi. Alamat jalan dan titik koordinat GPS akan terdeteksi otomatis dari kamera ponsel Anda.
                    </p>
                </div>
                <div class="pt-3 border-t border-sand-100 dark:border-sand-900 text-xs text-sand-600 dark:text-sand-500 flex items-center gap-1.5">
                    <flux:icon name="camera" class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                    <span>Otomatis via GPS HP</span>
                </div>
            </div>

            <!-- Langkah 2 -->
            <div class="p-5 sm:p-6 rounded-xl border border-sand-100 dark:border-sand-900 bg-white dark:bg-sand-950 flex flex-col justify-between space-y-4 shadow-2xs">
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs font-mono shadow-xs">
                            02
                        </span>
                    </div>
                    <h4 class="font-sans text-base sm:text-lg text-sand-975 dark:text-sand-100 font-bold">
                        Dukungan Suara Warga
                    </h4>
                    <p class="text-xs sm:text-sm text-sand-600 dark:text-sand-500 leading-relaxed font-sans">
                        Warga sekitar cukup menekan tombol Dukung (Upvote). Semakin banyak dukungan warga, laporan otomatis naik ke prioritas Kritis.
                    </p>
                </div>
                <div class="pt-3 border-t border-sand-100 dark:border-sand-900 text-xs text-sand-600 dark:text-sand-500 flex items-center gap-1.5">
                    <flux:icon name="hand-thumb-up" class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                    <span>1 Akun = 1 Suara Sah</span>
                </div>
            </div>

            <!-- Langkah 3 -->
            <div class="p-5 sm:p-6 rounded-xl border border-sand-100 dark:border-sand-900 bg-white dark:bg-sand-950 flex flex-col justify-between space-y-4 shadow-2xs">
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs font-mono shadow-xs">
                            03
                        </span>
                    </div>
                    <h4 class="font-sans text-base sm:text-lg text-sand-975 dark:text-sand-100 font-bold">
                        Respon Dinas &amp; Selesai
                    </h4>
                    <p class="text-xs sm:text-sm text-sand-600 dark:text-sand-500 leading-relaxed font-sans">
                        Petugas dinas dan dinas terkait menindaklanjuti laporan. Perkembangan perbaikan dipantau terbuka hingga berstatus Selesai.
                    </p>
                </div>
                <div class="pt-3 border-t border-sand-100 dark:border-sand-900 text-xs text-sand-600 dark:text-sand-500 flex items-center gap-1.5">
                    <flux:icon name="check-circle" class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
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
        <a href="{{ route('heatmap.index') }}" class="group p-5 sm:p-6 rounded-xl border border-sand-100 dark:border-sand-900 bg-white dark:bg-sand-950 hover:border-emerald-300 dark:hover:border-emerald-800 flex gap-4 shadow-2xs transition">
            <span class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <flux:icon name="map" class="w-5 h-5" />
            </span>
            <div class="space-y-1.5 min-w-0">
                <h3 class="font-sans text-base sm:text-lg text-sand-975 dark:text-sand-100 font-bold">Peta Sebaran Masalah Kota</h3>
                <p class="text-sm text-sand-600 dark:text-sand-500 leading-relaxed">
                    Lihat titik kerusakan jalan, lampu padam, dan fasilitas kota lainnya langsung di peta.
                </p>
                <span class="inline-flex items-center gap-1 pt-1 text-sm font-semibold text-emerald-700 dark:text-emerald-400 group-hover:underline">
                    Buka Peta Sebaran <span aria-hidden="true">&rarr;</span>
                </span>
            </div>
        </a>

        <!-- Asisten Cerdas AI @Sira -->
        <a href="{{ route('reports.index') }}" class="group p-5 sm:p-6 rounded-xl border border-sand-100 dark:border-sand-900 bg-white dark:bg-sand-950 hover:border-emerald-300 dark:hover:border-emerald-800 flex gap-4 shadow-2xs transition">
            <span class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <flux:icon name="sparkles" class="w-5 h-5" />
            </span>
            <div class="space-y-1.5 min-w-0">
                <h3 class="font-sans text-base sm:text-lg text-sand-975 dark:text-sand-100 font-bold">Tanya Asisten @Sira</h3>
                <p class="text-sm text-sand-600 dark:text-sand-500 leading-relaxed">
                    Ketik <span class="px-1.5 py-0.5 rounded-md bg-sand-50 dark:bg-sand-925 text-emerald-700 dark:text-emerald-400 font-semibold">@Sira</span> di kolom komentar untuk mendapat analisa keparahan dan saran tindakan.
                </p>
                <span class="inline-flex items-center gap-1 pt-1 text-sm font-semibold text-emerald-700 dark:text-emerald-400 group-hover:underline">
                    Lihat di Dasbor <span aria-hidden="true">&rarr;</span>
                </span>
            </div>
        </a>
    </section>

    <!-- ================================================================= -->
    <!-- BAGIAN 5: AJAKAN LAPOR (PENUTUP HALAMAN)                          -->
    <!-- ================================================================= -->
    <section class="p-6 sm:p-8 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/50 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
        <div class="space-y-1">
            <h2 class="font-sans text-lg sm:text-xl text-sand-975 dark:text-sand-100 font-bold tracking-tight">
                Lihat sesuatu yang rusak hari ini?
            </h2>
            <p class="text-sm text-sand-600 dark:text-sand-400">
                Satu foto dari Anda membantu warga lain dan mempercepat penanganan dinas terkait.
            </p>
        </div>
        <x-report-cta class="shrink-0" />
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
