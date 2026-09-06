@props([
    'totalReports' => 0,
    'criticalCount' => 0,
    'urgentCount' => 0,
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
                    <div class="inline-flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#EDF3EC] text-[#346538] dark:bg-[#1C281E] dark:text-[#82C78A]">
                            Sistem Informasi Ruang Aman
                        </span>
                        <span class="text-xs font-mono text-[#787774] dark:text-[#8E8D8A]">
                            Platform Terbuka Pengawasan Fasilitas Kota
                        </span>
                    </div>

                    <h1 class="font-sans text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-[#111111] dark:text-[#EDEDEC] leading-[1.15]">
                        Suarakan realitas fasilitas publik tanpa birokrasi berbelit.
                    </h1>

                    <p class="text-xs sm:text-sm text-[#787774] dark:text-[#9B9B97] leading-relaxed font-sans max-w-xl">
                        Dokumentasikan jalan berlubang, saluran tersumbat, lampu penerangan padam, atau fasilitas terbengkalai dengan bukti foto dan koordinat OpenFreeMap. Komunitas mem-voting tingkat urgensi perbaikan, berkolaborasi dengan asisten cerdas <span class="font-mono text-emerald-600 dark:text-emerald-400 font-semibold">@Sira</span>, serta memantau penanganan secara transparan.
                    </p>

                    <div class="pt-2 flex flex-wrap items-center gap-3 font-mono text-xs">
                        <a href="{{ route('reports.create') }}" class="px-4 py-2.5 bg-[#111111] hover:bg-[#2A2A2A] active:scale-[0.98] text-white dark:bg-[#EDEDEC] dark:text-[#111111] rounded-[6px] transition duration-150 font-medium inline-flex items-center justify-center gap-1.5">
                            <span>+ Buat Laporan Baru</span>
                        </a>
                        <a href="{{ route('heatmap.index') }}" class="px-4 py-2.5 border border-[#EAEAEA] dark:border-[#282828] hover:bg-[#F7F6F3] dark:hover:bg-[#1C1C1C] text-[#111111] dark:text-[#EDEDEC] rounded-[6px] transition duration-150 inline-flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#9F2F2D]"></span>
                            <span>Peta Sebaran Masalah &rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Metric Ledger Bar -->
                <div class="pt-6 border-t border-[#EAEAEA] dark:border-[#222222] grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 font-mono">
                    <div class="p-2.5 sm:p-3 rounded-[6px] bg-[#FBFBFA] dark:bg-[#181818] border border-[#EAEAEA] dark:border-[#262626]">
                        <div class="text-[10px] uppercase text-[#787774] dark:text-[#8E8D8A] tracking-wider">
                            <span class="sm:hidden">Total</span>
                            <span class="hidden sm:inline">Total Laporan</span>
                        </div>
                        <div class="text-lg sm:text-2xl font-bold text-[#111111] dark:text-[#EDEDEC] mt-0.5">
                            {{ number_format($totalReports) }}
                        </div>
                        <div class="text-[10px] text-[#787774] mt-0.5 hidden sm:block">Terdokumentasi</div>
                    </div>

                    <div class="p-2.5 sm:p-3 rounded-[6px] bg-[#FBFBFA] dark:bg-[#181818] border border-[#EAEAEA] dark:border-[#262626]">
                        <div class="text-[10px] uppercase text-[#9F2F2D] tracking-wider">
                            <span class="sm:hidden">Kritis</span>
                            <span class="hidden sm:inline">Kritis (&ge;100)</span>
                        </div>
                        <div class="text-lg sm:text-2xl font-bold text-[#9F2F2D] mt-0.5">
                            {{ number_format($criticalCount) }}
                        </div>
                        <div class="text-[10px] text-[#9F2F2D]/80 mt-0.5 hidden sm:block">Prioritas Tinggi</div>
                    </div>

                    <div class="p-2.5 sm:p-3 rounded-[6px] bg-[#FBFBFA] dark:bg-[#181818] border border-[#EAEAEA] dark:border-[#262626]">
                        <div class="text-[10px] uppercase text-[#956400] dark:text-[#E0BE69] tracking-wider">
                            <span class="sm:hidden">Mendesak</span>
                            <span class="hidden sm:inline">Mendesak (&ge;50)</span>
                        </div>
                        <div class="text-lg sm:text-2xl font-bold text-[#956400] dark:text-[#E0BE69] mt-0.5">
                            {{ number_format($urgentCount) }}
                        </div>
                        <div class="text-[10px] text-[#787774] mt-0.5 hidden sm:block">Dukungan Warga</div>
                    </div>

                    <div class="p-2.5 sm:p-3 rounded-[6px] bg-[#FBFBFA] dark:bg-[#181818] border border-[#EAEAEA] dark:border-[#262626]">
                        <div class="text-[10px] uppercase text-[#346538] dark:text-[#82C78A] tracking-wider">
                            <span class="sm:hidden">Selesai</span>
                            <span class="hidden sm:inline">Tuntas Selesai</span>
                        </div>
                        <div class="text-lg sm:text-2xl font-bold text-[#346538] dark:text-[#82C78A] mt-0.5">
                            {{ number_format($resolvedCount) }}
                        </div>
                        <div class="text-[10px] text-[#346538]/80 dark:text-[#82C78A]/80 mt-0.5 hidden sm:block">Tertangani</div>
                    </div>
                </div>
            </div>

            <!-- Sisi Kanan: Card Prioritas Utama -->
            <div class="lg:col-span-5 w-full">
                <x-leaderboard :reports="$criticalReports" />
            </div>
        </div>
    </div>
</section>
