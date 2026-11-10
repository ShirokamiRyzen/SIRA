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
    <div class="border border-sand-100 dark:border-sand-900 bg-white dark:bg-sand-975 rounded-xl p-4 sm:p-8 lg:p-12 relative overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
            <!-- Sisi Kiri: Editorial, Headline, CTA & Metrik -->
            <div class="lg:col-span-7 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="inline-flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-success-tint text-success dark:bg-success-deep dark:text-success-soft">
                            Sistem Informasi Ruang Aman
                        </span>
                        <span class="text-xs font-mono text-sand-600 dark:text-sand-500">
                            Platform Terbuka Pengawasan Fasilitas Kota
                        </span>
                    </div>

                    <h1 class="font-sans text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-sand-975 dark:text-sand-100 leading-[1.15]">
                        Suarakan realitas fasilitas publik tanpa birokrasi berbelit.
                    </h1>

                    <p class="text-xs sm:text-sm text-sand-600 dark:text-sand-500 leading-relaxed font-sans max-w-xl">
                        Dokumentasikan jalan berlubang, saluran tersumbat, lampu penerangan padam, atau fasilitas terbengkalai dengan bukti foto dan koordinat OpenFreeMap. Komunitas mem-voting tingkat urgensi perbaikan, berkolaborasi dengan asisten cerdas <span class="font-mono text-emerald-600 dark:text-emerald-400 font-semibold">@Sira</span>, serta memantau penanganan secara transparan.
                    </p>

                    <div class="pt-2 flex flex-wrap items-center gap-3 font-mono text-xs">
                        <a href="{{ route('reports.create') }}" class="px-4 py-2.5 bg-sand-975 hover:bg-sand-850 active:scale-[0.98] text-white dark:bg-sand-100 dark:text-sand-975 rounded-md transition duration-150 font-medium inline-flex items-center justify-center gap-1.5">
                            <span>+ Buat Laporan Baru</span>
                        </a>
                        <a href="{{ route('heatmap.index') }}" class="px-4 py-2.5 border border-sand-100 dark:border-sand-850 hover:bg-sand-50 dark:hover:bg-sand-925 text-sand-975 dark:text-sand-100 rounded-md transition duration-150 inline-flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-danger"></span>
                            <span>Peta Sebaran Masalah &rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Metric Ledger Bar -->
                <div class="pt-6 border-t border-sand-100 dark:border-sand-900 grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 font-mono">
                    <div class="p-2.5 sm:p-3 rounded-md bg-sand-25 dark:bg-sand-950 border border-sand-100 dark:border-sand-900">
                        <div class="text-[10px] uppercase text-sand-600 dark:text-sand-500 tracking-wider">
                            <span class="sm:hidden">Total</span>
                            <span class="hidden sm:inline">Total Laporan</span>
                        </div>
                        <div class="text-lg sm:text-2xl font-bold text-sand-975 dark:text-sand-100 mt-0.5">
                            {{ number_format($totalReports) }}
                        </div>
                        <div class="text-[10px] text-sand-600 mt-0.5 hidden sm:block">Terdokumentasi</div>
                    </div>

                    <div class="p-2.5 sm:p-3 rounded-md bg-sand-25 dark:bg-sand-950 border border-sand-100 dark:border-sand-900">
                        <div class="text-[10px] uppercase text-danger tracking-wider">
                            <span class="sm:hidden">Kritis</span>
                            <span class="hidden sm:inline">Kritis (&ge;100)</span>
                        </div>
                        <div class="text-lg sm:text-2xl font-bold text-danger mt-0.5">
                            {{ number_format($criticalCount) }}
                        </div>
                        <div class="text-[10px] text-danger/80 mt-0.5 hidden sm:block">Prioritas Tinggi</div>
                    </div>

                    <div class="p-2.5 sm:p-3 rounded-md bg-sand-25 dark:bg-sand-950 border border-sand-100 dark:border-sand-900">
                        <div class="text-[10px] uppercase text-warning dark:text-warning-soft tracking-wider">
                            <span class="sm:hidden">Mendesak</span>
                            <span class="hidden sm:inline">Mendesak (&ge;50)</span>
                        </div>
                        <div class="text-lg sm:text-2xl font-bold text-warning dark:text-warning-soft mt-0.5">
                            {{ number_format($urgentCount) }}
                        </div>
                        <div class="text-[10px] text-sand-600 mt-0.5 hidden sm:block">Dukungan Warga</div>
                    </div>

                    <div class="p-2.5 sm:p-3 rounded-md bg-sand-25 dark:bg-sand-950 border border-sand-100 dark:border-sand-900">
                        <div class="text-[10px] uppercase text-success dark:text-success-soft tracking-wider">
                            <span class="sm:hidden">Selesai</span>
                            <span class="hidden sm:inline">Tuntas Selesai</span>
                        </div>
                        <div class="text-lg sm:text-2xl font-bold text-success dark:text-success-soft mt-0.5">
                            {{ number_format($resolvedCount) }}
                        </div>
                        <div class="text-[10px] text-success/80 dark:text-success-soft/80 mt-0.5 hidden sm:block">Tertangani</div>
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
