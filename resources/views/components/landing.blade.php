@props([
    'totalReports' => 0,
    'criticalCount' => 0,
    'resolvedCount' => 0,
    'criticalReports' => [],
])

<!-- Component: Landing Showcase & Hero Section -->
<section class="space-y-12 mb-14">
    <!-- Hero Editorial Headline & Big Card -->
    <div class="border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] rounded-[8px] p-4 sm:p-8 lg:p-12 relative overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
            <!-- Sisi Kiri: Editorial, Headline, CTA & Metrik -->
            <div class="lg:col-span-7 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#EDF3EC] text-[#346538] dark:bg-[#1C281E] dark:text-[#82C78A]">
                            Sistem Pengawasan Warga
                        </span>
                        <span class="text-[11px] font-mono text-[#787774]">v1.0 Open-Ledger</span>
                    </div>

                    <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-normal tracking-tight text-[#111111] dark:text-[#EDEDEC] leading-[1.15]">
                        Suarakan realitas fasilitas publik tanpa birokrasi berbelit.
                    </h1>

                    <p class="text-xs sm:text-sm text-[#787774] dark:text-[#9B9B97] leading-relaxed font-sans max-w-xl">
                        Dokumentasikan kerusakan jalan, tumpukan sampah, atau fasilitas mangkrak dengan bukti foto dan koordinat OpenFreeMap. Komunitas mem-voting tingkat urgensi hingga mencapai status prioritas tertinggi.
                    </p>

                    <div class="pt-2 flex flex-wrap items-center gap-3 font-mono text-xs">
                        <a href="{{ route('reports.create') }}" class="px-4 py-2.5 bg-[#111111] hover:bg-[#2A2A2A] active:scale-[0.98] text-white dark:bg-[#EDEDEC] dark:text-[#111111] rounded-[6px] transition duration-150 font-medium">
                            + Buat Laporan Baru
                        </a>
                        <a href="{{ route('heatmap.index') }}" class="px-4 py-2.5 border border-[#EAEAEA] dark:border-[#282828] hover:bg-[#F7F6F3] dark:hover:bg-[#1C1C1C] text-[#111111] dark:text-[#EDEDEC] rounded-[6px] transition duration-150">
                            Buka Peta Heatmap &rarr;
                        </a>
                    </div>
                </div>

                <!-- Metric Ledger Bar -->
                <div class="pt-6 border-t border-[#EAEAEA] dark:border-[#222222] grid grid-cols-2 sm:grid-cols-3 gap-4 font-mono">
                    <div>
                        <div class="text-[10px] uppercase text-[#787774] tracking-wider">Total Laporan Warga</div>
                        <div class="text-xl sm:text-2xl font-bold text-[#111111] dark:text-[#EDEDEC] mt-0.5">
                            {{ number_format($totalReports) }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] uppercase text-[#9F2F2D] tracking-wider">Prioritas Kritis (Critical)</div>
                        <div class="text-xl sm:text-2xl font-bold text-[#9F2F2D] mt-0.5">
                            {{ number_format($criticalCount) }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] uppercase text-[#346538] tracking-wider">Penanganan / Selesai</div>
                        <div class="text-xl sm:text-2xl font-bold text-[#346538] mt-0.5">
                            {{ number_format($resolvedCount) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sisi Kanan: Card Prioritas Utama -->
            <div class="lg:col-span-5 w-full">
                <x-leaderboard :reports="$criticalReports" />
            </div>
        </div>
    </div>

    <!-- Bento Grid Fitur Utilitarian -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 font-sans">
        <!-- Bento 1 -->
        <div class="p-6 rounded-[8px] border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] space-y-2">
            <div class="text-xs font-mono font-bold text-[#111111] dark:text-[#EDEDEC] flex items-center space-x-1.5">
                <span class="w-2 h-2 rounded-full bg-[#111111] dark:bg-[#EDEDEC]"></span>
                <span>01. OpenFreeMap &amp; Reverse Geocode</span>
            </div>
            <p class="text-xs text-[#787774] dark:text-[#9B9B97] leading-relaxed">
                Pilih titik lokasi pada peta terbuka. Nama jalan, kelurahan, dan kecamatan teridentifikasi otomatis tanpa dependensi API komersial berbayar.
            </p>
        </div>

        <!-- Bento 2 -->
        <div class="p-6 rounded-[8px] border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] space-y-2">
            <div class="text-xs font-mono font-bold text-[#111111] dark:text-[#EDEDEC] flex items-center space-x-1.5">
                <span class="w-2 h-2 rounded-full bg-[#9F2F2D]"></span>
                <span>02. Crowdsourced Voting Tier</span>
            </div>
            <p class="text-xs text-[#787774] dark:text-[#9B9B97] leading-relaxed">
                Laporan tidak diverifikasi oleh birokrat tunggal. Dukungan vote komunitas yang menentukan apakah suatu masalah naik ke status Trending, Urgent, atau Critical.
            </p>
        </div>

        <!-- Bento 3 -->
        <div class="p-6 rounded-[8px] border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] space-y-2">
            <div class="text-xs font-mono font-bold text-[#111111] dark:text-[#EDEDEC] flex items-center space-x-1.5">
                <span class="w-2 h-2 rounded-full bg-[#1F6C9F]"></span>
                <span>03. WebGL Heatmap GPU</span>
            </div>
            <p class="text-xs text-[#787774] dark:text-[#9B9B97] leading-relaxed">
                Visualisasi titik-titik panas masalah kota secara menyeluruh. Semakin tinggi skor vote laporan, semakin pekat intensitas warna panas yang terpancar.
            </p>
        </div>
    </div>
</section>
