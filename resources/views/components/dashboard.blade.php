@props([
    'reports',
    'availableCities' => [],
    'availableDistricts' => [],
    'criticalReports' => [],
    'sort' => 'trending',
])

<!-- Component: Dashboard Laporan & Feed Komunitas -->
<div class="space-y-8">
    <!-- Filter & Query Control Bar -->
    <div class="bg-white dark:bg-[#141414] p-4 sm:p-5 rounded-[8px] border border-[#EAEAEA] dark:border-[#222222]">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Text Input -->
            <div class="lg:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari judul masalah atau alamat..."
                    class="w-full px-3.5 py-2 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] focus:outline-none focus:border-[#111111] dark:focus:border-[#EDEDEC]">
            </div>

            <!-- Filter Kota Dropdown -->
            <div>
                <select name="city" class="w-full px-3.5 py-2 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] focus:outline-none focus:border-[#111111]">
                    <option value="">Semua Kota/Kab</option>
                    @foreach ($availableCities as $city)
                        <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Kecamatan Dropdown -->
            <div>
                <select name="district" class="w-full px-3.5 py-2 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] focus:outline-none focus:border-[#111111]">
                    <option value="">Semua Kecamatan</option>
                    @foreach ($availableDistricts as $dist)
                        <option value="{{ $dist }}" {{ request('district') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Tier & Submit Button -->
            <div class="flex items-center space-x-2">
                <select name="rank_tier" class="w-full px-3 py-2 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] focus:outline-none focus:border-[#111111]">
                    <option value="">Semua Tier</option>
                    <option value="critical" {{ request('rank_tier') == 'critical' ? 'selected' : '' }}>Critical (100+)</option>
                    <option value="urgent" {{ request('rank_tier') == 'urgent' ? 'selected' : '' }}>Urgent (50+)</option>
                    <option value="trending" {{ request('rank_tier') == 'trending' ? 'selected' : '' }}>Trending (10+)</option>
                    <option value="normal" {{ request('rank_tier') == 'normal' ? 'selected' : '' }}>Normal</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-[#111111] hover:bg-[#2A2A2A] text-white dark:bg-[#EDEDEC] dark:text-[#111111] rounded-[6px] text-xs font-mono transition shrink-0">
                    Filter
                </button>
            </div>
        </form>

        <!-- Sorting Control Tabs -->
        <div class="flex items-center justify-between mt-4 pt-3 border-t border-[#EAEAEA] dark:border-[#222222] text-xs font-mono">
            <span class="text-[#787774]">Urutkan Data:</span>
            <div class="flex items-center space-x-1">
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'trending']) }}"
                   class="px-2.5 py-1 rounded-[4px] transition {{ ($sort ?? 'trending') === 'trending' ? 'bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111]' : 'text-[#787774] hover:bg-[#EAEAEA]/50' }}">
                   Trending
                </a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'top_score']) }}"
                   class="px-2.5 py-1 rounded-[4px] transition {{ ($sort ?? '') === 'top_score' ? 'bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111]' : 'text-[#787774] hover:bg-[#EAEAEA]/50' }}">
                   Skor Tertinggi
                </a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}"
                   class="px-2.5 py-1 rounded-[4px] transition {{ ($sort ?? '') === 'latest' ? 'bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111]' : 'text-[#787774] hover:bg-[#EAEAEA]/50' }}">
                   Terbaru
                </a>
            </div>
        </div>
    </div>

    <!-- Grid Content (Reports + Leaderboard) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Feed Laporan (2 Kolom) -->
        <div class="lg:col-span-2 space-y-6">
            @if ($reports->isEmpty())
                <div class="border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] p-12 rounded-[8px] text-center space-y-2">
                    <span class="font-mono text-xs text-[#787774] block">[KOSONG]</span>
                    <h3 class="font-serif text-lg font-medium text-[#111111] dark:text-[#EDEDEC]">
                        Tidak ditemukan laporan yang sesuai kriteria pencarian
                    </h3>
                    <p class="text-xs text-[#787774] max-w-sm mx-auto font-sans">
                        Gunakan filter lain atau jadilah yang pertama mendokumentasikan keluhan publik.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('reports.create') }}" class="inline-block px-3.5 py-2 bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111] text-xs font-mono rounded-[6px]">
                            + Buat Laporan Baru
                        </a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach ($reports as $report)
                        <x-report-card :report="$report" />
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pt-4 font-mono text-xs">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>

        <!-- Sidebar Leaderboard (1 Kolom) -->
        <div class="space-y-6">
            <x-leaderboard :reports="$criticalReports" />
        </div>
    </div>
</div>
