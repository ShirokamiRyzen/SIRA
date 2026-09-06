@props([
    'reports',
    'availableCities' => [],
    'availableDistricts' => [],
    'criticalReports' => [],
    'sort' => 'trending',
])

<!-- Component: Dashboard Laporan & Feed Komunitas -->
<div id="dashboard" class="space-y-8 scroll-mt-24 transition-opacity duration-200">
    <!-- Filter & Query Control Bar -->
    <div class="bg-white dark:bg-[#141414] p-4 sm:p-5 rounded-[8px] border border-[#EAEAEA] dark:border-[#222222]">
        <form method="GET" action="{{ route('reports.index', [], false) }}#dashboard" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <input type="hidden" name="sort" value="{{ request('sort', 'trending') }}">
            <!-- Search Text Input -->
            <div class="lg:col-span-2">
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
            <!-- Filter Status Quick Pills -->
            <div class="flex items-center space-x-1.5 overflow-x-auto">
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
