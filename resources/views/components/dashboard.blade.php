@props([
    'reports',
    'availableCities' => [],
    'availableDistricts' => [],
    'criticalReports' => [],
    'sort' => 'trending',
    'multiIssueCount' => 0,
    'myReportsCount' => 0,
])

<!-- Component: Dashboard Laporan & Feed Komunitas (Sederhana & Boomer-Proof) -->
<div id="dashboard" class="space-y-6 scroll-mt-24 transition-opacity duration-200">
    <!-- Panel Filter Sederhana & Ramah Pengguna -->
    <div class="bg-white dark:bg-[#141414] p-4 sm:p-5 rounded-[8px] border border-[#EAEAEA] dark:border-[#222222] space-y-4 shadow-xs">
        <form method="GET" action="{{ route('reports.index', [], false) }}#dashboard" class="space-y-4">
            <input type="hidden" name="sort" value="{{ request('sort', 'trending') }}">
            @if (request('my_reports'))
                <input type="hidden" name="my_reports" value="1">
            @endif

            <!-- Baris 1: Kolom Pencarian Cepat & Pilihan Wilayah -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                <!-- Input Cari Teks -->
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari keluhan (misal: jalan berlubang, lampu padam, Dago)..."
                        class="w-full px-3.5 py-2.5 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-[#FBFBFA] dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] focus:outline-none focus:border-[#111111] dark:focus:border-[#EDEDEC]">
                    @if (request('search'))
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}#dashboard"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-mono text-[#999999] hover:text-[#111111] dark:hover:text-white"
                            title="Hapus pencarian">&times;</a>
                    @endif
                </div>

                <!-- Dropdown Pilihan Wilayah / Kecamatan -->
                <div class="sm:w-64">
                    <select name="district" onchange="this.form.requestSubmit()"
                        class="w-full px-3.5 py-2.5 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] focus:outline-none focus:border-[#111111] cursor-pointer">
                        <option value="">Semua Wilayah / Kecamatan</option>
                        @foreach ($availableDistricts as $dist)
                            <option value="{{ $dist }}" {{ request('district') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tombol Submit & Reset -->
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit"
                        class="w-full sm:w-auto px-5 py-2.5 bg-[#111111] hover:bg-[#2A2A2A] text-white dark:bg-[#EDEDEC] dark:text-[#111111] dark:hover:bg-white rounded-[6px] text-xs font-mono font-medium transition cursor-pointer">
                        Cari
                    </button>

                    @if (request()->hasAny(['search', 'city', 'district', 'rank_tier', 'status', 'issue_type', 'my_reports']))
                        <a href="{{ route('reports.index', [], false) }}#dashboard"
                            class="px-3 py-2.5 border border-[#EAEAEA] dark:border-[#282828] bg-[#FBFBFA] dark:bg-[#1A1A1A] hover:bg-[#F0F0EF] text-[#9F2F2D] dark:text-[#E88C8A] rounded-[6px] text-xs font-mono transition text-center whitespace-nowrap"
                            title="Hapus semua filter">
                            Reset &times;
                        </a>
                    @endif
                </div>
            </div>

            <!-- Baris 2: Tab Status Laporan yang Besar & Kontras Tinggi -->
            <div class="pt-3 border-t border-[#EAEAEA] dark:border-[#222222] flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs font-mono">
                <!-- Status Tabs -->
                <div class="flex items-center space-x-1.5 overflow-x-auto pb-1 md:pb-0">
                    <span class="text-[#787774] dark:text-[#8E8D8A] shrink-0 mr-1 font-medium">Status:</span>

                    <!-- Semua -->
                    <a href="{{ request()->fullUrlWithQuery(['status' => null, 'rank_tier' => null]) }}#dashboard"
                       class="px-3 py-1.5 rounded-[6px] transition shrink-0 font-medium {{ !request('status') && !request('rank_tier') ? 'bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111] font-bold shadow-xs' : 'text-[#787774] dark:text-[#9B9B97] hover:bg-[#EAEAEA]/60 dark:hover:bg-[#222222] hover:text-[#111111] dark:hover:text-white' }}">
                       Semua
                    </a>

                    <!-- Kritis -->
                    <a href="{{ request()->fullUrlWithQuery(['rank_tier' => 'critical', 'status' => null]) }}#dashboard"
                       class="px-3 py-1.5 rounded-[6px] transition shrink-0 font-medium border {{ request('rank_tier') === 'critical' ? 'bg-[#9F2F2D] border-[#9F2F2D] text-white font-bold shadow-xs' : 'text-[#9F2F2D] dark:text-[#E88C8A] bg-[#FDEBEC] dark:bg-[#2D1517] border-[#9F2F2D]/20 hover:bg-[#F9D6D8] dark:hover:bg-[#3D1D20]' }}">
                       Kritis (Prioritas)
                    </a>

                    <!-- Sedang Diproses -->
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'in_progress', 'rank_tier' => null]) }}#dashboard"
                       class="px-3 py-1.5 rounded-[6px] transition shrink-0 font-medium border {{ request('status') === 'in_progress' ? 'bg-indigo-600 border-indigo-600 text-white font-bold shadow-xs' : 'text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/40 border-indigo-200/50 dark:border-indigo-800/40 hover:bg-indigo-100 dark:hover:bg-indigo-900/50' }}">
                       Sedang Diproses
                    </a>

                    <!-- Selesai -->
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'resolved', 'rank_tier' => null]) }}#dashboard"
                       class="px-3 py-1.5 rounded-[6px] transition shrink-0 font-medium border {{ request('status') === 'resolved' ? 'bg-emerald-600 border-emerald-600 text-white font-bold shadow-xs' : 'text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200/50 dark:border-emerald-800/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/50' }}">
                       Selesai
                    </a>

                    <span class="text-[#D4D4D4] dark:text-[#333333] mx-1">|</span>

                    <!-- Multi Masalah Tag -->
                    <a href="{{ request()->fullUrlWithQuery(['issue_type' => request('issue_type') === 'multi' ? null : 'multi']) }}#dashboard"
                       class="px-2.5 py-1.5 rounded-[6px] transition shrink-0 font-medium inline-flex items-center gap-1 border {{ request('issue_type') === 'multi' ? 'bg-violet-700 border-violet-700 text-white font-bold shadow-xs' : 'text-violet-700 dark:text-violet-300 bg-violet-50 dark:bg-violet-950/40 border-violet-200/50 dark:border-violet-800/40 hover:bg-violet-100 dark:hover:bg-violet-900/50 hover:text-violet-800 dark:hover:text-violet-200' }}">
                       <flux:icon name="squares-2x2" class="w-3 h-3 shrink-0" />
                       <span>Multi Masalah ({{ $multiIssueCount }})</span>
                    </a>

                    @auth
                        <!-- Laporan Saya Tag -->
                        <a href="{{ request()->fullUrlWithQuery(['my_reports' => request('my_reports') ? null : 1]) }}#dashboard"
                           class="px-2.5 py-1.5 rounded-[6px] transition shrink-0 font-medium inline-flex items-center gap-1 border {{ request('my_reports') ? 'bg-emerald-700 border-emerald-700 text-white font-bold shadow-xs' : 'text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200/50 dark:border-emerald-800/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 hover:text-emerald-800 dark:hover:text-emerald-200' }}">
                           <flux:icon name="user" class="w-3 h-3 shrink-0" />
                           <span>Laporan Saya ({{ $myReportsCount }})</span>
                        </a>
                    @endauth
                </div>

                <!-- Kontrol Urutan -->
                <div class="flex items-center space-x-1 shrink-0">
                    <span class="text-[#787774] dark:text-[#8E8D8A] mr-1">Urutan:</span>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'trending']) }}#dashboard"
                       class="px-2.5 py-1 rounded-[4px] transition {{ ($sort ?? 'trending') === 'trending' ? 'bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111] font-bold shadow-xs' : 'text-[#787774] dark:text-[#8E8D8A] hover:bg-[#EAEAEA]/60 dark:hover:bg-[#222222] hover:text-[#111111] dark:hover:text-[#EDEDEC]' }}">
                       Trending
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'top_score']) }}#dashboard"
                       class="px-2.5 py-1 rounded-[4px] transition {{ ($sort ?? '') === 'top_score' ? 'bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111] font-bold shadow-xs' : 'text-[#787774] dark:text-[#8E8D8A] hover:bg-[#EAEAEA]/60 dark:hover:bg-[#222222] hover:text-[#111111] dark:hover:text-[#EDEDEC]' }}">
                       Skor Tertinggi
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}#dashboard"
                       class="px-2.5 py-1 rounded-[4px] transition {{ ($sort ?? '') === 'latest' ? 'bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111] font-bold shadow-xs' : 'text-[#787774] dark:text-[#8E8D8A] hover:bg-[#EAEAEA]/60 dark:hover:bg-[#222222] hover:text-[#111111] dark:hover:text-[#EDEDEC]' }}">
                       Terbaru
                    </a>
                </div>
            </div>

            <!-- Opsi Filter Tambahan (Disembunyikan Rapi agar Tidak Membingungkan) -->
            <details class="group pt-2 text-xs font-mono">
                <summary class="cursor-pointer text-[#787774] dark:text-[#8E8D8A] hover:text-[#111111] dark:hover:text-[#EDEDEC] inline-flex items-center gap-1 select-none">
                    <span>Opsi filter spesifik (Kota &amp; Tier)...</span>
                    <span class="text-[10px] group-open:rotate-180 transition-transform">&darr;</span>
                </summary>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3">
                    <div>
                        <label class="block text-[11px] text-[#787774] mb-1">Pilih Kota / Kabupaten:</label>
                        <select name="city" onchange="this.form.requestSubmit()"
                            class="w-full px-3 py-2 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC]">
                            <option value="">Semua Kota/Kab</option>
                            @foreach ($availableCities as $city)
                                <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] text-[#787774] mb-1">Pilih Tingkat Urgensi (Tier):</label>
                        <select name="rank_tier" onchange="this.form.requestSubmit()"
                            class="w-full px-3 py-2 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC]">
                            <option value="">Semua Tingkat</option>
                            <option value="critical" {{ request('rank_tier') == 'critical' ? 'selected' : '' }}>Kritis (100+ Suara)</option>
                            <option value="urgent" {{ request('rank_tier') == 'urgent' ? 'selected' : '' }}>Mendesak (50+ Suara)</option>
                            <option value="trending" {{ request('rank_tier') == 'trending' ? 'selected' : '' }}>Trending (10+ Suara)</option>
                            <option value="normal" {{ request('rank_tier') == 'normal' ? 'selected' : '' }}>Normal</option>
                        </select>
                    </div>
                </div>
            </details>
        </form>
    </div>

    <!-- Ringkasan Hasil Pencarian & Daftar Kartu Laporan -->
    <div class="space-y-5">
        <!-- Informasi Jumlah Laporan yang Ditemukan -->
        <div class="flex items-center justify-between text-xs font-mono text-[#787774] dark:text-[#8E8D8A] px-1">
            <span>
                Menampilkan <strong class="text-[#111111] dark:text-[#EDEDEC]">{{ $reports->total() }}</strong> laporan
                @if (request('my_reports') && Auth::check())
                    <span class="inline-flex items-center gap-1 font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded border border-emerald-200/60 dark:border-emerald-800/40 ml-1">
                        milik Anda (@<span>{{ Auth::user()->username }}</span>)
                    </span>
                @endif
                @if (request('district'))
                    di <span class="font-bold text-[#111111] dark:text-[#EDEDEC]">{{ request('district') }}</span>
                @endif
                @if (request('search'))
                    untuk kata kunci <span class="italic text-[#111111] dark:text-[#EDEDEC]">"{{ request('search') }}"</span>
                @endif
            </span>

            @if ($reports->hasPages())
                <span>Halaman {{ $reports->currentPage() }} dari {{ $reports->lastPage() }}</span>
            @endif
        </div>

        @if ($reports->isEmpty())
            <div class="border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] p-10 sm:p-14 rounded-[8px] text-center space-y-3 shadow-xs">
                <div class="w-10 h-10 rounded-full bg-[#F4F4F3] dark:bg-[#202020] text-[#787774] dark:text-[#999999] flex items-center justify-center mx-auto text-sm font-mono">
                    ?
                </div>
                <h3 class="font-sans text-base sm:text-lg font-bold text-[#111111] dark:text-[#EDEDEC]">
                    Tidak ada laporan yang sesuai kriteria pencarian
                </h3>
                <p class="text-xs text-[#787774] dark:text-[#9B9B97] max-w-sm mx-auto font-sans leading-relaxed">
                    Coba ganti kata kunci pencarian, ubah filter wilayah, atau jadilah yang pertama melaporkan masalah ini.
                </p>
                <div class="pt-2 flex items-center justify-center gap-2">
                    <a href="{{ route('reports.index') }}#dashboard" class="px-3.5 py-2 border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-[#111111] dark:text-[#EDEDEC] text-xs font-mono rounded-[6px]">
                        Lihat Semua Laporan
                    </a>
                    <a href="{{ route('reports.create') }}" class="px-3.5 py-2 bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111] text-xs font-mono rounded-[6px]">
                        + Buat Laporan Baru
                    </a>
                </div>
            </div>
        @else
            <!-- Top Pagination -->
            @if ($reports->hasPages())
                <div class="border-b border-[#EAEAEA] dark:border-[#222222] pb-3 font-mono text-xs">
                    {{ $reports->links() }}
                </div>
            @endif

            <!-- Grid 3 Kolom Responsif -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
                @foreach ($reports as $report)
                    <x-report-card :report="$report" />
                @endforeach
            </div>

            <!-- Pagination Bawah (Hanya satu pagination bersih) -->
            @if ($reports->hasPages())
                <div class="pt-4 border-t border-[#EAEAEA] dark:border-[#222222] font-mono text-xs">
                    {{ $reports->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

<script>
    (function () {
        function initDashboardAjax() {
            const dashboard = document.getElementById('dashboard');
            if (!dashboard || dashboard.dataset.ajaxInitialized === 'true') return;
            dashboard.dataset.ajaxInitialized = 'true';

            function fetchDashboard(url) {
                const targetUrl = url.includes('#') ? url : url + '#dashboard';
                dashboard.classList.add('opacity-40', 'pointer-events-none');

                fetch(targetUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newDashboard = doc.getElementById('dashboard');
                    if (newDashboard) {
                        dashboard.innerHTML = newDashboard.innerHTML;
                        window.history.pushState(null, '', targetUrl);

                        const rect = dashboard.getBoundingClientRect();
                        if (rect.top < -50 || rect.top > window.innerHeight) {
                            dashboard.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }

                        dashboard.dataset.ajaxInitialized = 'false';
                        initDashboardAjax();
                    } else {
                        window.location.href = targetUrl;
                    }
                })
                .catch(err => {
                    console.error('AJAX Filter error:', err);
                    window.location.href = targetUrl;
                })
                .finally(() => {
                    dashboard.classList.remove('opacity-40', 'pointer-events-none');
                });
            }

            // Tangani submit form secara asinkron
            const form = dashboard.querySelector('form');
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(form);
                    const params = new URLSearchParams();
                    for (const [key, val] of formData.entries()) {
                        if (val && val.trim() !== '') {
                            params.append(key, val.trim());
                        }
                    }
                    const baseUrl = form.action.split('?')[0].split('#')[0];
                    const queryStr = params.toString();
                    const finalUrl = baseUrl + (queryStr ? '?' + queryStr : '') + '#dashboard';
                    fetchDashboard(finalUrl);
                });
            }

            // Tangani klik tautan filter & navigasi halaman (pagination)
            dashboard.querySelectorAll('a').forEach(link => {
                const href = link.getAttribute('href');
                if (!href || href.startsWith('#')) return;

                if (link.pathname.includes('/report/new') || link.pathname.match(/\/reports\/\d+/)) {
                    return;
                }

                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    fetchDashboard(link.href);
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initDashboardAjax);
        } else {
            initDashboardAjax();
        }

        window.addEventListener('popstate', function () {
            const dashboard = document.getElementById('dashboard');
            if (dashboard) {
                dashboard.dataset.ajaxInitialized = 'false';
                initDashboardAjax();
            }
        });
    })();
</script>
