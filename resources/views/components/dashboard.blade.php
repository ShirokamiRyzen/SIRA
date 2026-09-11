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
    <!-- Panel Filter Terpadu, Rapi, & Proporsional -->
    <div class="bg-white dark:bg-[#141414] p-3.5 sm:p-4 rounded-[8px] border border-[#EAEAEA] dark:border-[#222222] shadow-xs">
        <form method="GET" action="{{ route('reports.index', [], false) }}#dashboard" class="space-y-3">
            @if (request('my_reports'))
                <input type="hidden" name="my_reports" value="1">
            @endif

            <!-- Baris 1: Kolom Pencarian Cepat & Aksi -->
            <div class="flex flex-col sm:flex-row items-stretch gap-2">
                <!-- Input Cari Teks dengan Ikon -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#787774] dark:text-[#8E8D8A]">
                        <flux:icon name="magnifying-glass" class="w-3.5 h-3.5" />
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari keluhan (misal: jalan berlubang, lampu padam, Dago)..."
                        class="w-full pl-9 pr-8 py-2 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-[#FBFBFA] dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] focus:outline-none focus:border-[#111111] dark:focus:border-[#EDEDEC]">
                    @if (request('search'))
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}#dashboard"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs font-mono text-[#999999] hover:text-[#111111] dark:hover:text-white p-1"
                            title="Hapus kata kunci">&times;</a>
                    @endif
                </div>

                <!-- Tombol Submit & Reset -->
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="submit"
                        class="w-full sm:w-auto px-4 py-2 bg-[#111111] hover:bg-[#2A2A2A] text-white dark:bg-[#EDEDEC] dark:text-[#111111] dark:hover:bg-white rounded-[6px] text-xs font-mono font-medium transition cursor-pointer">
                        Cari
                    </button>
                    @if (request()->hasAny(['search', 'city', 'district', 'rank_tier', 'status', 'issue_type', 'my_reports']))
                        <a href="{{ route('reports.index', [], false) }}#dashboard"
                            class="px-2.5 py-2 border border-[#EAEAEA] dark:border-[#282828] bg-[#FBFBFA] dark:bg-[#1A1A1A] hover:bg-[#F0F0EF] text-[#9F2F2D] dark:text-[#E88C8A] rounded-[6px] text-xs font-mono transition text-center whitespace-nowrap"
                            title="Reset semua filter">
                            Reset &times;
                        </a>
                    @endif
                </div>
            </div>

            <!-- Baris 2: Dropdown Filter Terpadu (Grid Proporsional, Tidak Melebar Ekstrem) -->
            <div class="pt-2.5 border-t border-[#EAEAEA] dark:border-[#222222] grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2 text-xs font-mono">
                <!-- Dropdown Status -->
                <div>
                    <label class="block text-[10px] uppercase tracking-wider text-[#787774] dark:text-[#8E8D8A] mb-1">Status</label>
                    <select name="status" onchange="this.form.requestSubmit()"
                        class="w-full px-2.5 py-1.5 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] focus:outline-none focus:border-[#111111] cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Menunggu Respon</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Sedang Diproses</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Tuntas Selesai</option>
                    </select>
                </div>

                <!-- Dropdown Prioritas (Tier) -->
                <div>
                    <label class="block text-[10px] uppercase tracking-wider text-[#787774] dark:text-[#8E8D8A] mb-1">Prioritas</label>
                    <select name="rank_tier" onchange="this.form.requestSubmit()"
                        class="w-full px-2.5 py-1.5 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] focus:outline-none focus:border-[#111111] cursor-pointer">
                        <option value="">Semua Prioritas</option>
                        <option value="critical" {{ request('rank_tier') === 'critical' ? 'selected' : '' }}>Prioritas Kritis</option>
                        <option value="urgent" {{ request('rank_tier') === 'urgent' ? 'selected' : '' }}>Prioritas Mendesak</option>
                        <option value="normal" {{ request('rank_tier') === 'normal' ? 'selected' : '' }}>Reguler</option>
                    </select>
                </div>

                <!-- Dropdown Wilayah / Kecamatan -->
                <div>
                    <label class="block text-[10px] uppercase tracking-wider text-[#787774] dark:text-[#8E8D8A] mb-1">Kecamatan</label>
                    <select name="district" onchange="this.form.requestSubmit()"
                        class="w-full px-2.5 py-1.5 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] focus:outline-none focus:border-[#111111] cursor-pointer">
                        <option value="">Semua Kecamatan</option>
                        @foreach ($availableDistricts as $dist)
                            <option value="{{ $dist }}" {{ request('district') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown Kota / Kabupaten -->
                <div>
                    <label class="block text-[10px] uppercase tracking-wider text-[#787774] dark:text-[#8E8D8A] mb-1">Kota/Kab</label>
                    <select name="city" onchange="if(this.form.district) this.form.district.value = ''; this.form.requestSubmit()"
                        class="w-full px-2.5 py-1.5 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] focus:outline-none focus:border-[#111111] cursor-pointer">
                        <option value="">Semua Kota</option>
                        @foreach ($availableCities as $city)
                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown Urutan (Sort) -->
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-[10px] uppercase tracking-wider text-[#787774] dark:text-[#8E8D8A] mb-1">Urutan</label>
                    <select name="sort" onchange="this.form.requestSubmit()"
                        class="w-full px-2.5 py-1.5 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] focus:outline-none focus:border-[#111111] cursor-pointer">
                        <option value="trending" {{ ($sort ?? 'trending') === 'trending' ? 'selected' : '' }}>Trending</option>
                        <option value="top_score" {{ ($sort ?? '') === 'top_score' ? 'selected' : '' }}>Skor Tertinggi</option>
                        <option value="latest" {{ ($sort ?? '') === 'latest' ? 'selected' : '' }}>Laporan Terbaru</option>
                    </select>
                </div>
            </div>

            <!-- Quick Filter Badges (Pill Cepat Multi Masalah & Laporan Saya) -->
            <div class="pt-2 flex items-center gap-1.5 flex-wrap text-xs font-mono border-t border-[#EAEAEA]/60 dark:border-[#222222]/60">
                <span class="text-[11px] text-[#787774] dark:text-[#8E8D8A] mr-1">Filter Khusus:</span>

                <!-- Multi Masalah Toggle -->
                <a href="{{ request()->fullUrlWithQuery(['issue_type' => request('issue_type') === 'multi' ? null : 'multi']) }}#dashboard"
                   class="px-2.5 py-1 rounded-[5px] transition text-[11px] inline-flex items-center gap-1 border {{ request('issue_type') === 'multi' ? 'bg-violet-700 border-violet-700 text-white font-bold shadow-2xs' : 'text-violet-700 dark:text-violet-300 bg-violet-50 dark:bg-violet-950/40 border-violet-200/50 dark:border-violet-800/40 hover:bg-violet-100 dark:hover:bg-violet-900/50' }}">
                   <flux:icon name="squares-2x2" class="w-3 h-3 shrink-0" />
                   <span>Multi Masalah ({{ $multiIssueCount }})</span>
                </a>

                @auth
                    <!-- Laporan Saya Toggle -->
                    <a href="{{ request()->fullUrlWithQuery(['my_reports' => request('my_reports') ? null : 1]) }}#dashboard"
                       class="px-2.5 py-1 rounded-[5px] transition text-[11px] inline-flex items-center gap-1 border {{ request('my_reports') ? 'bg-emerald-700 border-emerald-700 text-white font-bold shadow-2xs' : 'text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200/50 dark:border-emerald-800/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/50' }}">
                       <flux:icon name="user" class="w-3 h-3 shrink-0" />
                       <span>Laporan Saya ({{ $myReportsCount }})</span>
                    </a>
                @endauth

                @if (request()->hasAny(['search', 'city', 'district', 'rank_tier', 'status', 'issue_type', 'my_reports']))
                    <span class="text-[#D4D4D4] dark:text-[#333333] mx-1">|</span>
                    <a href="{{ route('reports.index', [], false) }}#dashboard" class="text-[11px] text-[#9F2F2D] dark:text-[#E88C8A] hover:underline inline-flex items-center gap-0.5">
                        <span>Bersihkan Filter &times;</span>
                    </a>
                @endif
            </div>
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
