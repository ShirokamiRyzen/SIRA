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
    <!-- Panel Filter: cari, saring, dan filter cepat -->
    @php
        $fieldClass = 'w-full px-3 py-2 rounded-lg border border-sand-200 dark:border-sand-850 bg-white dark:bg-sand-950 text-sm text-sand-975 dark:text-sand-100 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 cursor-pointer';
        $labelClass = 'block text-xs font-medium text-sand-600 dark:text-sand-500 mb-1.5';
        $hasFilter = request()->hasAny(['search', 'city', 'district', 'rank_tier', 'status', 'issue_type', 'my_reports']);
    @endphp
    <div class="bg-white dark:bg-sand-950 p-4 sm:p-5 rounded-2xl border border-sand-100 dark:border-sand-900 shadow-2xs">
        <form method="GET" action="{{ route('reports.index', [], false) }}#dashboard" class="space-y-4">
            @if (request('my_reports'))
                <input type="hidden" name="my_reports" value="1">
            @endif

            <!-- Baris 1: Pencarian teks -->
            <div class="flex items-stretch gap-2">
                <div class="relative flex-1 min-w-0">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-sand-500">
                        <flux:icon name="magnifying-glass" class="w-4 h-4" />
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari keluhan, mis. jalan berlubang"
                        class="w-full pl-9 pr-9 py-2.5 rounded-xl border border-sand-200 dark:border-sand-850 bg-sand-25 dark:bg-sand-975 text-sm text-sand-975 dark:text-sand-100 placeholder:text-sand-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                    @if (request('search'))
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}#dashboard"
                            class="absolute right-2 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full flex items-center justify-center text-sand-500 hover:text-sand-975 dark:hover:text-white hover:bg-sand-100 dark:hover:bg-sand-900"
                            title="Hapus kata kunci">&times;</a>
                    @endif
                </div>
                <button type="submit"
                    class="shrink-0 px-4 sm:px-5 py-2.5 rounded-xl btn-primary text-sm font-semibold shadow-sm transition cursor-pointer">
                    Cari
                </button>
            </div>

            <!-- Baris 2: Saringan -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                <div>
                    <label class="{{ $labelClass }}">Status</label>
                    <select name="status" onchange="this.form.requestSubmit()" class="{{ $fieldClass }}">
                        <option value="">Semua status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Menunggu Respon</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Sedang Diproses</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Tuntas Selesai</option>
                    </select>
                </div>

                <div>
                    <label class="{{ $labelClass }}">Prioritas</label>
                    <select name="rank_tier" onchange="this.form.requestSubmit()" class="{{ $fieldClass }}">
                        <option value="">Semua prioritas</option>
                        <option value="critical" {{ request('rank_tier') === 'critical' ? 'selected' : '' }}>Prioritas Kritis</option>
                        <option value="urgent" {{ request('rank_tier') === 'urgent' ? 'selected' : '' }}>Prioritas Mendesak</option>
                        <option value="normal" {{ request('rank_tier') === 'normal' ? 'selected' : '' }}>Reguler</option>
                    </select>
                </div>

                <div>
                    <label class="{{ $labelClass }}">Kecamatan</label>
                    <select name="district" onchange="this.form.requestSubmit()" class="{{ $fieldClass }}">
                        <option value="">Semua kecamatan</option>
                        @foreach ($availableDistricts as $dist)
                            <option value="{{ $dist }}" {{ request('district') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="{{ $labelClass }}">Kota/Kab</label>
                    <select name="city" onchange="if(this.form.district) this.form.district.value = ''; this.form.requestSubmit()" class="{{ $fieldClass }}">
                        <option value="">Semua kota</option>
                        @foreach ($availableCities as $city)
                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <label class="{{ $labelClass }}">Urutan</label>
                    <select name="sort" onchange="this.form.requestSubmit()" class="{{ $fieldClass }}">
                        <option value="trending" {{ ($sort ?? 'trending') === 'trending' ? 'selected' : '' }}>Trending</option>
                        <option value="top_score" {{ ($sort ?? '') === 'top_score' ? 'selected' : '' }}>Skor Tertinggi</option>
                        <option value="latest" {{ ($sort ?? '') === 'latest' ? 'selected' : '' }}>Laporan Terbaru</option>
                    </select>
                </div>
            </div>

            <!-- Baris 3: Filter cepat (satu gaya: netral saat mati, hijau saat aktif) -->
            @php
                $pillOn = 'bg-emerald-600 border-emerald-600 text-white';
                $pillOff = 'border-sand-200 dark:border-sand-850 text-sand-700 dark:text-sand-300 hover:bg-sand-50 dark:hover:bg-sand-925';
            @endphp
            <div class="pt-4 border-t border-sand-100 dark:border-sand-900 flex items-center gap-2 flex-wrap">
                <a href="{{ request()->fullUrlWithQuery(['issue_type' => request('issue_type') === 'multi' ? null : 'multi']) }}#dashboard"
                   class="px-3 py-1.5 rounded-full border text-xs font-semibold inline-flex items-center gap-1.5 transition {{ request('issue_type') === 'multi' ? $pillOn : $pillOff }}">
                   <flux:icon name="squares-2x2" class="w-3.5 h-3.5 shrink-0" />
                   <span>Multi Masalah ({{ $multiIssueCount }})</span>
                </a>

                @auth
                    <a href="{{ request()->fullUrlWithQuery(['my_reports' => request('my_reports') ? null : 1]) }}#dashboard"
                       class="px-3 py-1.5 rounded-full border text-xs font-semibold inline-flex items-center gap-1.5 transition {{ request('my_reports') ? $pillOn : $pillOff }}">
                       <flux:icon name="user" class="w-3.5 h-3.5 shrink-0" />
                       <span>Laporan Saya ({{ $myReportsCount }})</span>
                    </a>
                @endauth

                @if ($hasFilter)
                    <a href="{{ route('reports.index', [], false) }}#dashboard" class="ml-auto text-xs font-semibold text-danger dark:text-danger-soft hover:underline">
                        Bersihkan filter &times;
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Ringkasan Hasil Pencarian & Daftar Kartu Laporan -->
    <div class="space-y-5">
        <!-- Informasi Jumlah Laporan yang Ditemukan -->
        <div class="flex items-center justify-between text-sm text-sand-600 dark:text-sand-500 px-1">
            <span>
                Menampilkan <strong class="text-sand-975 dark:text-sand-100">{{ $reports->total() }}</strong> laporan
                @if (request('my_reports') && Auth::check())
                    <span class="inline-flex items-center gap-1 font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded border border-emerald-200/60 dark:border-emerald-800/40 ml-1">
                        milik Anda (@<span>{{ Auth::user()->username }}</span>)
                    </span>
                @endif
                @if (request('district'))
                    di <span class="font-bold text-sand-975 dark:text-sand-100">{{ request('district') }}</span>
                @endif
                @if (request('search'))
                    untuk kata kunci <span class="italic text-sand-975 dark:text-sand-100">"{{ request('search') }}"</span>
                @endif
            </span>

            @if ($reports->hasPages())
                <span>Halaman {{ $reports->currentPage() }} dari {{ $reports->lastPage() }}</span>
            @endif
        </div>

        @if ($reports->isEmpty())
            <div class="border border-sand-100 dark:border-sand-900 bg-white dark:bg-sand-975 p-10 sm:p-14 rounded-xl text-center space-y-3 shadow-xs">
                <div class="w-10 h-10 rounded-full bg-sand-50 dark:bg-sand-925 text-sand-600 dark:text-sand-500 flex items-center justify-center mx-auto text-sm font-mono">
                    ?
                </div>
                <h3 class="font-sans text-base sm:text-lg font-bold text-sand-975 dark:text-sand-100">
                    Tidak ada laporan yang sesuai kriteria pencarian
                </h3>
                <p class="text-xs text-sand-600 dark:text-sand-500 max-w-sm mx-auto font-sans leading-relaxed">
                    Coba ganti kata kunci pencarian, ubah filter wilayah, atau jadilah yang pertama melaporkan masalah ini.
                </p>
                <div class="pt-2 flex items-center justify-center gap-2">
                    <a href="{{ route('reports.index') }}#dashboard" class="inline-flex items-center h-9 px-3.5 rounded-lg border border-sand-200 dark:border-sand-850 bg-white dark:bg-sand-950 hover:bg-sand-50 dark:hover:bg-sand-925 text-sand-975 dark:text-sand-100 text-sm font-semibold transition">
                        Lihat Semua Laporan
                    </a>
                    <x-report-cta size="sm" />
                </div>
            </div>
        @else
            <!-- Top Pagination -->
            @if ($reports->hasPages())
                <div class="border-b border-sand-100 dark:border-sand-900 pb-3 font-mono text-xs">
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
                <div class="pt-4 border-t border-sand-100 dark:border-sand-900 font-mono text-xs">
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
