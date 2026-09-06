@extends('layouts.app')

@section('title', 'Cara Kerja & Fitur Utama — SIRA')

@section('content')
<div class="space-y-12 sm:space-y-16 py-2 sm:py-4">
    <!-- Gabungan: Cara Kerja & Fitur Utama Sistem SIRA -->
    <section id="cara-kerja-fitur" class="space-y-6 scroll-mt-20">
        <!-- Target Anchor ID untuk menjaga kompatibilitas hash URL #cara-kerja dan #fitur -->
        <div id="fitur" class="scroll-mt-24"></div>
        <div id="cara-kerja" class="scroll-mt-24"></div>

        <!-- Header Panduan (Bahasa Sederhana & Ramah Warga) -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-[#EAEAEA] dark:border-[#222222] pb-5">
            <div>
                <div class="inline-flex items-center gap-2 mb-1.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                        Sistem Pengawasan Warga
                    </span>
                    <span class="font-mono text-xs uppercase tracking-wider text-[#787774] dark:text-[#8E8D8A]">
                        Panduan Mudah
                    </span>
                </div>
                <h1 class="font-sans text-2xl sm:text-3xl text-[#111111] dark:text-[#EDEDEC] font-bold tracking-tight">
                    Cara Kerja &amp; Fitur SIRA
                </h1>
                <p class="text-xs sm:text-sm text-[#787774] dark:text-[#9B9B97] font-sans mt-1 max-w-2xl leading-relaxed">
                    Laporkan jalan berlubang, lampu mati, atau fasilitas umum rusak di sekitar Anda dalam 3 langkah mudah.
                </p>
            </div>

            <!-- Tab Switcher (Semua / Cara Kerja / Fitur Utama) -->
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

        <!-- Panel 1: Alur Kerja (3 Langkah Praktis untuk Semua Kalangan) -->
        <div id="panel-cara-kerja" class="space-y-4">
            <div class="flex items-center space-x-2 text-xs font-mono text-[#787774] dark:text-[#8E8D8A]">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 dark:bg-emerald-400"></span>
                <span class="uppercase tracking-wider font-semibold text-emerald-800 dark:text-emerald-300">3 Langkah Mudah Melapor</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5 select-text">
                <!-- Kartu Langkah 1 -->
                <div class="p-5 sm:p-6 rounded-[8px] border border-[#EAEAEA] dark:border-[#262626] bg-white dark:bg-[#161615] flex flex-col justify-between shadow-xs space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="w-8 h-8 rounded-[6px] bg-emerald-600 text-white flex items-center justify-center font-bold text-xs font-mono shadow-xs">
                                01
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-[4px] text-[10px] font-mono tracking-wider uppercase bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                Foto &amp; Lokasi
                            </span>
                        </div>
                        <h3 class="font-sans text-base sm:text-lg text-[#111111] dark:text-[#EDEDEC] font-bold">
                            Foto Bukti Kerusakan
                        </h3>
                        <p class="text-xs sm:text-sm text-[#787774] dark:text-[#9B9B97] leading-relaxed">
                            Ambil foto kerusakan fasilitas secara langsung di tempat kejadian. Titik lokasi GPS jalan dan kecamatan akan terdeteksi otomatis.
                        </p>
                    </div>
                    <div class="pt-3 border-t border-[#EAEAEA] dark:border-[#262626] text-[11px] font-mono text-[#787774] dark:text-[#8E8D8A] flex items-center gap-1.5">
                        <flux:icon name="map-pin" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                        <span>Deteksi otomatis via GPS HP</span>
                    </div>
                </div>

                <!-- Kartu Langkah 2 -->
                <div class="p-5 sm:p-6 rounded-[8px] border border-[#EAEAEA] dark:border-[#262626] bg-white dark:bg-[#161615] flex flex-col justify-between shadow-xs space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="w-8 h-8 rounded-[6px] bg-sky-600 text-white flex items-center justify-center font-bold text-xs font-mono shadow-xs">
                                02
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-[4px] text-[10px] font-mono tracking-wider uppercase bg-sky-50 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300 border border-sky-200 dark:border-sky-800/60">
                                Suara Warga
                            </span>
                        </div>
                        <h3 class="font-sans text-base sm:text-lg text-[#111111] dark:text-[#EDEDEC] font-bold">
                            Dukungan Warga (Vote)
                        </h3>
                        <p class="text-xs sm:text-sm text-[#787774] dark:text-[#9B9B97] leading-relaxed">
                            Warga sekitar cukup menekan tombol Upvote (Dukung). Semakin banyak suara dukungan, status laporan naik menjadi Kritis agar diprioritaskan.
                        </p>
                    </div>
                    <div class="pt-3 border-t border-[#EAEAEA] dark:border-[#262626] text-[11px] font-mono text-[#787774] dark:text-[#8E8D8A] flex items-center gap-1.5">
                        <flux:icon name="hand-thumb-up" class="w-3.5 h-3.5 text-sky-600 dark:text-sky-400 shrink-0" />
                        <span>1 Akun Warga = 1 Suara Sah</span>
                    </div>
                </div>

                <!-- Kartu Langkah 3 -->
                <div class="p-5 sm:p-6 rounded-[8px] border border-[#EAEAEA] dark:border-[#262626] bg-white dark:bg-[#161615] flex flex-col justify-between shadow-xs space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="w-8 h-8 rounded-[6px] bg-indigo-600 text-white flex items-center justify-center font-bold text-xs font-mono shadow-xs">
                                03
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-[4px] text-[10px] font-mono tracking-wider uppercase bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/60">
                                Tindak Lanjut
                            </span>
                        </div>
                        <h3 class="font-sans text-base sm:text-lg text-[#111111] dark:text-[#EDEDEC] font-bold">
                            Respon Pemda &amp; Tuntas
                        </h3>
                        <p class="text-xs sm:text-sm text-[#787774] dark:text-[#9B9B97] leading-relaxed">
                            Pemerintah daerah dan dinas terkait memberikan tanggapan resmi di kolom komentar. Status laporan dipantau terbuka hingga perbaikan selesai.
                        </p>
                    </div>
                    <div class="pt-3 border-t border-[#EAEAEA] dark:border-[#262626] text-[11px] font-mono text-[#787774] dark:text-[#8E8D8A] flex items-center gap-1.5">
                        <flux:icon name="check-circle" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400 shrink-0" />
                        <span>Transparan dan dapat dipantau</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel 2: Fitur Utama (Sekat Jelas & Mudah Dipahami) -->
        <div id="panel-fitur" class="space-y-4 pt-4">
            <div class="flex items-center space-x-2 text-xs font-mono text-[#787774] dark:text-[#8E8D8A]">
                <span class="w-1.5 h-1.5 rounded-full bg-sky-600 dark:bg-sky-400"></span>
                <span class="uppercase tracking-wider font-semibold text-sky-800 dark:text-sky-300">Fitur Unggulan Platform</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5 select-text">
                <!-- Fitur 1: Peta Sebaran Interaktif -->
                <div class="p-5 sm:p-6 rounded-[8px] border border-[#EAEAEA] dark:border-[#262626] bg-white dark:bg-[#161615] flex flex-col justify-between shadow-xs space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-[4px] text-[10px] font-mono tracking-wider uppercase bg-sky-50 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300 border border-sky-200 dark:border-sky-800/60">
                                Peta Publik
                            </span>
                            <span class="font-mono text-[11px] text-[#787774] dark:text-[#8E8D8A]">Real-time</span>
                        </div>
                        <h3 class="font-sans text-base sm:text-lg text-[#111111] dark:text-[#EDEDEC] font-bold">
                            Peta Sebaran Masalah
                        </h3>
                        <p class="text-xs sm:text-sm text-[#787774] dark:text-[#9B9B97] leading-relaxed">
                            Pantau sebaran titik kerusakan fasilitas umum di peta wilayah interaktif. Memudahkan warga mengetahui kondisi jalan dan titik rawan kota.
                        </p>
                    </div>
                    <div class="pt-3 border-t border-[#EAEAEA] dark:border-[#262626] flex items-center justify-between text-xs font-mono">
                        <span class="text-[#787774] dark:text-[#8E8D8A]">Akses Terbuka</span>
                        <a href="{{ route('heatmap.index') }}" class="text-sky-600 dark:text-sky-400 hover:underline font-semibold flex items-center gap-1">
                            <span>Buka Peta</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Fitur 2: Asisten Cerdas @Sira -->
                <div class="p-5 sm:p-6 rounded-[8px] border border-[#EAEAEA] dark:border-[#262626] bg-white dark:bg-[#161615] flex flex-col justify-between shadow-xs space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-[4px] text-[10px] font-mono tracking-wider uppercase bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                Bantuan Cerdas
                            </span>
                            <span class="font-mono text-[11px] text-[#787774] dark:text-[#8E8D8A]">Asisten AI</span>
                        </div>
                        <h3 class="font-sans text-base sm:text-lg text-[#111111] dark:text-[#EDEDEC] font-bold">
                            Asisten Cerdas @Sira
                        </h3>
                        <p class="text-xs sm:text-sm text-[#787774] dark:text-[#9B9B97] leading-relaxed">
                            Ketik <code class="font-mono px-1 py-0.5 rounded bg-[#F4F4F3] dark:bg-[#202020] text-amber-700 dark:text-amber-400 font-bold">@Sira</code> di kolom komentar untuk mendapatkan analisa penyebab kerusakan fasilitas dan rekomendasi teknis penanganan.
                        </p>
                    </div>
                    <div class="pt-3 border-t border-[#EAEAEA] dark:border-[#262626] text-xs font-mono text-[#787774] dark:text-[#8E8D8A]">
                        Tersedia di setiap kolom komentar
                    </div>
                </div>

                <!-- Fitur 3: Respon Resmi Pemda -->
                <div class="p-5 sm:p-6 rounded-[8px] border border-[#EAEAEA] dark:border-[#262626] bg-white dark:bg-[#161615] flex flex-col justify-between shadow-xs space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-[4px] text-[10px] font-mono tracking-wider uppercase bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                Terverifikasi
                            </span>
                            <span class="font-mono text-[11px] text-[#787774] dark:text-[#8E8D8A]">Akun Resmi</span>
                        </div>
                        <h3 class="font-sans text-base sm:text-lg text-[#111111] dark:text-[#EDEDEC] font-bold">
                            Terkoneksi Pemda Resmi
                        </h3>
                        <p class="text-xs sm:text-sm text-[#787774] dark:text-[#9B9B97] leading-relaxed">
                            Akun dinas dan instansi daerah (berlencana biru terverifikasi) merespons langsung setiap laporan untuk memastikan transparansi perbaikan.
                        </p>
                    </div>
                    <div class="pt-3 border-t border-[#EAEAEA] dark:border-[#262626] text-xs font-mono text-[#787774] dark:text-[#8E8D8A]">
                        Akun resmi: @pemda_jabar, jateng, jatim
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Laporan Sorotan Acak Komunitas -->
    @if ($criticalReports->isNotEmpty())
        <section class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-[#EAEAEA] dark:border-[#222222] pb-4">
                <div>
                    <span class="font-mono text-xs uppercase tracking-wider text-emerald-600 dark:text-emerald-400 font-semibold">
                        Sorotan Warga
                    </span>
                    <h2 class="font-sans text-2xl sm:text-3xl text-[#111111] dark:text-[#EDEDEC] mt-1 font-bold">
                        Laporan Terkini dari Warga
                    </h2>
                </div>
                <a href="{{ route('reports.index') }}" class="text-xs font-mono text-[#111111] dark:text-[#EDEDEC] hover:underline underline-offset-2 flex items-center gap-1.5 shrink-0 font-medium">
                    <span>Lihat Seluruh Laporan ({{ number_format($totalReports) }})</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                @foreach ($criticalReports as $report)
                    <x-report-card :report="$report" />
                @endforeach
            </div>
        </section>
    @endif

    <!-- Kategori Masalah Fasilitas yang Dilaporkan -->
    <section class="p-6 sm:p-8 rounded-[8px] border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] space-y-5 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="font-mono text-[11px] uppercase tracking-wider text-[#787774] font-medium">Kategori Pengawasan</span>
                <h3 class="font-sans text-lg sm:text-xl text-[#111111] dark:text-[#EDEDEC] font-bold mt-0.5">
                    Fasilitas publik yang dapat dilaporkan
                </h3>
            </div>
            <a href="{{ route('reports.create') }}" class="text-xs font-mono text-[#111111] dark:text-[#EDEDEC] hover:underline underline-offset-2 shrink-0 font-medium">
                + Laporkan Sekarang &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 pt-2">
            <div class="p-4 rounded-[6px] border border-[#EAEAEA] dark:border-[#262626] bg-[#FBFBFA] dark:bg-[#181818] space-y-1">
                <div class="font-bold text-xs text-[#111111] dark:text-[#EDEDEC]">Infrastruktur</div>
                <p class="text-[11px] text-[#787774] dark:text-[#8E8D8A] leading-relaxed">Jalan rusak, jembatan retak, aspal berlubang, trotoar ambles.</p>
            </div>
            <div class="p-4 rounded-[6px] border border-[#EAEAEA] dark:border-[#262626] bg-[#FBFBFA] dark:bg-[#181818] space-y-1">
                <div class="font-bold text-xs text-[#111111] dark:text-[#EDEDEC]">Kelistrikan &amp; PJU</div>
                <p class="text-[11px] text-[#787774] dark:text-[#8E8D8A] leading-relaxed">Lampu jalan padam, kabel menjuntai, tiang miring, gardu meletup.</p>
            </div>
            <div class="p-4 rounded-[6px] border border-[#EAEAEA] dark:border-[#262626] bg-[#FBFBFA] dark:bg-[#181818] space-y-1">
                <div class="font-bold text-xs text-[#111111] dark:text-[#EDEDEC]">Lingkungan Hidup</div>
                <p class="text-[11px] text-[#787774] dark:text-[#8E8D8A] leading-relaxed">Tumpukan sampah liar, limbah beracun, got mampet bau menyengat.</p>
            </div>
            <div class="p-4 rounded-[6px] border border-[#EAEAEA] dark:border-[#262626] bg-[#FBFBFA] dark:bg-[#181818] space-y-1">
                <div class="font-bold text-xs text-[#111111] dark:text-[#EDEDEC]">Bencana Alam</div>
                <p class="text-[11px] text-[#787774] dark:text-[#8E8D8A] leading-relaxed">Banjir luapan kali, longsor tebing jalan, tanggul sungai jebol.</p>
            </div>
            <div class="p-4 rounded-[6px] border border-[#EAEAEA] dark:border-[#262626] bg-[#FBFBFA] dark:bg-[#181818] space-y-1">
                <div class="font-bold text-xs text-[#111111] dark:text-[#EDEDEC]">Fasilitas Umum</div>
                <p class="text-[11px] text-[#787774] dark:text-[#8E8D8A] leading-relaxed">Halte bus rusak, taman kota mangkrak, ubin difabel pecah.</p>
            </div>
        </div>
    </section>

    <!-- Banner Ajak Warga Berpartisipasi (CTA) -->
    <section class="p-6 sm:p-10 rounded-[8px] border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] flex flex-col sm:flex-row sm:items-center justify-between gap-6 shadow-xs">
        <div class="space-y-1.5 max-w-xl">
            <h3 class="font-sans text-xl sm:text-2xl text-[#111111] dark:text-[#EDEDEC] font-bold tracking-tight">
                Temukan fasilitas rusak di sekitar Anda?
            </h3>
            <p class="text-xs sm:text-sm text-[#787774] dark:text-[#8E8D8A] leading-relaxed">
                Setiap laporan Anda adalah langkah awal menuju transparansi informasi dan respon perbaikan fasilitas umum yang lebih cepat.
            </p>
        </div>
        <div class="flex items-center gap-3 shrink-0 flex-wrap">
            <a href="{{ route('reports.create') }}" class="px-5 py-2.5 rounded-[6px] bg-[#111111] hover:bg-[#2A2A2A] text-white dark:bg-[#EDEDEC] dark:text-[#111111] dark:hover:bg-white text-xs font-mono font-medium transition shadow-xs">
                + Buat Laporan Baru
            </a>
            <a href="{{ route('reports.index') }}" class="px-4 py-2.5 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#161615] text-[#111111] dark:text-[#EDEDEC] hover:bg-[#F7F6F3] dark:hover:bg-[#1F1F1E] text-xs font-mono font-medium transition">
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
