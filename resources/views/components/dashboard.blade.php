@props([
    'reports',
    'availableCities' => [],
    'availableDistricts' => [],
    'criticalReports' => [],
    'sort' => 'trending',
    'multiIssueCount' => 0,
])

<!-- Component: Dashboard Laporan & Feed Komunitas -->
<div id="dashboard" class="space-y-8 scroll-mt-24 transition-opacity duration-200">
    <!-- Filter & Query Control Bar -->
    <div class="bg-white dark:bg-[#141414] p-4 sm:p-5 rounded-[8px] border border-[#EAEAEA] dark:border-[#222222]">
        <form method="GET" action="{{ route('reports.index', [], false) }}#dashboard" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-3">
            <input type="hidden" name="sort" value="{{ request('sort', 'trending') }}">
            <!-- Search Text Input -->
            <div class="sm:col-span-2 lg:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari judul masalah atau alamat..."
                    class="w-full px-3.5 py-2 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] focus:outline-none focus:border-[#111111] dark:focus:border-[#EDEDEC]">
            </div>

            <!-- Filter Kota Dropdown -->
            <div>
                <select name="city" onchange="this.form.requestSubmit()" class="w-full px-3.5 py-2 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] focus:outline-none focus:border-[#111111]">
                    <option value="">Semua Kota/Kab</option>
                    @foreach ($availableCities as $city)
                        <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Kecamatan Dropdown -->
            <div>
                <select name="district" onchange="this.form.requestSubmit()" class="w-full px-3.5 py-2 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] focus:outline-none focus:border-[#111111]">
                    <option value="">Semua Kecamatan (A-Z)</option>
                    @foreach ($availableDistricts as $dist)
                        <option value="{{ $dist }}" {{ request('district') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Tipe Masalah Dropdown (Multi Masalah vs Tunggal) -->
            <div>
                <select name="issue_type" onchange="this.form.requestSubmit()" class="w-full px-3.5 py-2 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] focus:outline-none focus:border-[#111111]">
                    <option value="">Semua Tipe Masalah</option>
                    <option value="multi" {{ request('issue_type') == 'multi' ? 'selected' : '' }}>⊞ Multi Masalah ({{ $multiIssueCount }})</option>
                    <option value="single" {{ request('issue_type') == 'single' ? 'selected' : '' }}>Masalah Tunggal</option>
                </select>
            </div>

            <!-- Filter Status Dropdown -->
            <div>
                <select name="status" onchange="this.form.requestSubmit()" class="w-full px-3.5 py-2 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] focus:outline-none focus:border-[#111111]">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Ditangani</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Selesai (Resolved)</option>
                </select>
            </div>

            <!-- Filter Tier & Submit Button -->
            <div class="flex items-center space-x-2">
                <select name="rank_tier" onchange="this.form.requestSubmit()" class="w-full px-3 py-2 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] focus:outline-none focus:border-[#111111]">
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

        <!-- Status Pills & Sorting Control Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mt-4 pt-3 border-t border-[#EAEAEA] dark:border-[#222222] text-xs font-mono">
            <!-- Filter Status Quick Pills & Multi Masalah Filter -->
            <div class="flex items-center space-x-1.5 overflow-x-auto pb-1 sm:pb-0">
                <span class="text-[#787774] shrink-0">Status:</span>
                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}#dashboard"
                   class="px-2.5 py-1 rounded-[4px] transition shrink-0 {{ !request('status') ? 'bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111]' : 'text-[#787774] hover:bg-[#EAEAEA]/60' }}">
                   Semua
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}#dashboard"
                   class="px-2.5 py-1 rounded-[4px] transition shrink-0 {{ request('status') === 'active' ? 'bg-amber-600 text-white font-bold' : 'text-[#787774] hover:bg-[#EAEAEA]/60' }}">
                   ● Aktif
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'in_progress']) }}#dashboard"
                   class="px-2.5 py-1 rounded-[4px] transition shrink-0 {{ request('status') === 'in_progress' ? 'bg-indigo-600 text-white font-bold' : 'text-[#787774] hover:bg-[#EAEAEA]/60' }}">
                   ● Diproses
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'resolved']) }}#dashboard"
                   class="px-2.5 py-1 rounded-[4px] transition shrink-0 inline-flex items-center space-x-1 {{ request('status') === 'resolved' ? 'bg-emerald-600 text-white font-bold' : 'text-[#787774] hover:bg-[#EAEAEA]/60' }}">
                   <flux:icon name="check" class="w-3 h-3 shrink-0" />
                   <span>Selesai</span>
                </a>

                <span class="text-[#D4D4D4] dark:text-[#333333] mx-1">|</span>

                <!-- Quick Filter Pill: Multi Masalah -->
                <a href="{{ request()->fullUrlWithQuery(['issue_type' => request('issue_type') === 'multi' ? null : 'multi']) }}#dashboard"
                   class="px-2.5 py-1 rounded-[4px] transition shrink-0 inline-flex items-center gap-1.5 {{ request('issue_type') === 'multi' ? 'bg-violet-700 text-white font-bold ring-1 ring-violet-400' : 'text-violet-700 dark:text-violet-300 bg-violet-50 dark:bg-violet-950/40 border border-violet-200 dark:border-violet-800/60 hover:bg-violet-100 dark:hover:bg-violet-900/40' }}"
                   title="Saring hanya laporan yang memiliki multi-masalah di titik lokasi yang sama">
                   <flux:icon name="squares-2x2" class="w-3 h-3 shrink-0" />
                   <span>Multi Masalah ({{ $multiIssueCount }})</span>
                   @if (request('issue_type') === 'multi')
                       <span class="text-xs">&times;</span>
                   @endif
                </a>
            </div>

            <!-- Sorting Control Tabs -->
            <div class="flex items-center space-x-1 shrink-0">
                <span class="text-[#787774] mr-1">Urutkan:</span>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'trending']) }}#dashboard"
                   class="px-2.5 py-1 rounded-[4px] transition {{ ($sort ?? 'trending') === 'trending' ? 'bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111]' : 'text-[#787774] hover:bg-[#EAEAEA]/50' }}">
                   Trending
                </a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'top_score']) }}#dashboard"
                   class="px-2.5 py-1 rounded-[4px] transition {{ ($sort ?? '') === 'top_score' ? 'bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111]' : 'text-[#787774] hover:bg-[#EAEAEA]/50' }}">
                   Skor Tertinggi
                </a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}#dashboard"
                   class="px-2.5 py-1 rounded-[4px] transition {{ ($sort ?? '') === 'latest' ? 'bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111]' : 'text-[#787774] hover:bg-[#EAEAEA]/50' }}">
                   Terbaru
                </a>
            </div>
        </div>

        <!-- Scoped Filter Bar Khusus Multi-Masalah -->
        @if (request('issue_type') === 'multi')
            <div class="flex items-center justify-between flex-wrap gap-2 mt-3 pt-3 border-t border-violet-200/80 dark:border-violet-900/50 text-xs font-mono">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="text-violet-900 dark:text-violet-300 font-bold flex items-center gap-1 text-[11px]">
                        <flux:icon name="squares-2x2" class="w-3.5 h-3.5 text-violet-600 dark:text-violet-400 shrink-0" />
                        <span>Scope Khusus:</span>
                    </span>
                    <a href="{{ request()->fullUrlWithQuery(['issue_type' => 'multi', 'status' => null, 'rank_tier' => null]) }}#dashboard"
                       class="px-2.5 py-1 rounded-[4px] text-[11px] transition {{ !request('status') && !request('rank_tier') ? 'bg-violet-800 text-white font-bold' : 'text-violet-800 dark:text-violet-300 bg-violet-100/80 dark:bg-violet-950/60 hover:bg-violet-200' }}">
                       Semua Multi-Masalah
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['issue_type' => 'multi', 'rank_tier' => 'urgent', 'status' => null]) }}#dashboard"
                       class="px-2.5 py-1 rounded-[4px] text-[11px] transition inline-flex items-center gap-1 {{ request('rank_tier') === 'urgent' ? 'bg-amber-600 text-white font-bold' : 'text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100' }}">
                       <flux:icon name="exclamation-triangle" class="w-3 h-3 shrink-0" />
                       <span>Mendesak / Urgent</span>
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['issue_type' => 'multi', 'status' => 'active', 'rank_tier' => null]) }}#dashboard"
                       class="px-2.5 py-1 rounded-[4px] text-[11px] transition {{ request('status') === 'active' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-[#202020] hover:bg-slate-200' }}">
                       ● Aktif
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['issue_type' => 'multi', 'status' => 'resolved', 'rank_tier' => null]) }}#dashboard"
                       class="px-2.5 py-1 rounded-[4px] text-[11px] transition inline-flex items-center gap-1 {{ request('status') === 'resolved' ? 'bg-emerald-600 text-white font-bold' : 'text-emerald-800 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100' }}">
                       <flux:icon name="check" class="w-3 h-3 shrink-0" />
                       <span>Selesai</span>
                    </a>
                </div>
                <a href="{{ request()->fullUrlWithQuery(['issue_type' => null, 'status' => null, 'rank_tier' => null]) }}#dashboard" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-[11px] underline">
                    Reset Scope &times;
                </a>
            </div>
        @endif
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
                <!-- Top Pagination -->
                @if ($reports->hasPages())
                    <div class="border-b border-[#EAEAEA] dark:border-[#222222] pb-3 font-mono text-xs">
                        {{ $reports->links() }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach ($reports as $report)
                        <x-report-card :report="$report" />
                    @endforeach
                </div>

                <!-- Bottom Pagination -->
                @if ($reports->hasPages())
                    <div class="pt-4 border-t border-[#EAEAEA] dark:border-[#222222] font-mono text-xs">
                        {{ $reports->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Sidebar Leaderboard (1 Kolom) -->
        <div class="space-y-6">
            <x-leaderboard :reports="$criticalReports" />
        </div>
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

            // Form submit intercept
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

            // Links intercept (status pills, sort tabs, pagination)
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
