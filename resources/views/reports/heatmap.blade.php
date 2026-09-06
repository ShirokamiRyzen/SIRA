@extends('layouts.app')

@section('title', 'Peta Heatmap Sebaran Masalah - SIRA')

@section('content')
<div class="space-y-6">
    <!-- Header & Ringkasan Sebaran -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-[#141414] p-6 rounded-3xl border border-slate-200 dark:border-[#222222] shadow-sm">
        <div>
            <div class="inline-flex items-center space-x-1.5 text-xs font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 border border-rose-200/80 dark:border-rose-900/50 px-2.5 py-0.5 rounded-full mb-1">
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                <span>Visualisasi Kepadatan Geospasial</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-[#EDEDEC] tracking-tight">Peta Heatmap Laporan Komunitas</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-[#888888] mt-0.5">
                Area dengan warna merah membara menunjukkan konsentrasi laporan masalah dan dukungan voting yang tinggi.
            </p>
        </div>

        <!-- Tier Stats Badges -->
        <div class="flex flex-wrap items-center gap-2 text-xs">
            <div class="px-3 py-1.5 rounded-xl bg-slate-900 dark:bg-[#222222] text-white font-bold">
                Total: {{ $totalReports }} Laporan
            </div>
            <div class="px-3 py-1.5 rounded-xl bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 font-bold border border-rose-200 dark:border-rose-900/50">
                Critical: {{ $tierCounts['critical'] }}
            </div>
            <div class="px-3 py-1.5 rounded-xl bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 font-bold border border-amber-200 dark:border-amber-900/50">
                Urgent: {{ $tierCounts['urgent'] }}
            </div>
            <div class="px-3 py-1.5 rounded-xl bg-teal-100 text-teal-800 dark:bg-teal-950/60 dark:text-teal-300 font-bold border border-teal-200 dark:border-teal-900/50">
                Trending: {{ $tierCounts['trending'] }}
            </div>
        </div>
    </div>

    <!-- Filter Kategori Masalah Interaktif -->
    <div class="bg-white dark:bg-[#141414] p-4 sm:p-5 rounded-3xl border border-slate-200 dark:border-[#222222] shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <h2 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-[#EDEDEC] tracking-tight">
                    Filter Kategori Laporan
                </h2>
                <span id="activeFilterBadge" class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                    Semua Kategori ({{ $totalReports }})
                </span>
            </div>
            <div class="text-[11px] text-slate-400 dark:text-[#888888]">
                Klik kategori untuk memfilter titik panas dan icon visual pada peta
            </div>
        </div>

        <!-- Tombol Filter Kategori -->
        <div class="flex flex-wrap items-center gap-2" id="categoryFilterContainer">
            <button
                type="button"
                data-category="all"
                class="cat-filter-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer bg-slate-900 text-white dark:bg-emerald-500 dark:text-slate-950 shadow-xs ring-2 ring-slate-900/20 dark:ring-emerald-400/30"
            >
                <flux:icon name="squares-2x2" class="w-3.5 h-3.5" />
                <span>Semua</span>
                <span class="ml-0.5 text-[10px] px-1.5 py-0.5 rounded-full bg-white/20 dark:bg-slate-950/20 font-mono">{{ $totalReports }}</span>
            </button>

            @foreach($categories as $key => $cat)
                <button
                    type="button"
                    data-category="{{ $key }}"
                    class="cat-filter-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer border border-slate-200 dark:border-[#282828] bg-slate-50 hover:bg-slate-100 dark:bg-[#1C1C1C] dark:hover:bg-[#252525] text-slate-700 dark:text-[#CCCCCC]"
                >
                    <flux:icon name="{{ $cat['icon'] }}" class="w-3.5 h-3.5 shrink-0" style="color: {{ $cat['color'] }}" />
                    <span>{{ $cat['label'] }}</span>
                    <span class="ml-0.5 text-[10px] px-1.5 py-0.5 rounded-full bg-slate-200 dark:bg-[#2C2C2C] text-slate-700 dark:text-[#AAAAAA] font-mono">
                        {{ $categoryCounts[$key] ?? 0 }}
                    </span>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Peta Heatmap Kontainer -->
    <div class="relative w-full h-[650px] rounded-3xl border border-slate-300 dark:border-[#282828] shadow-md overflow-hidden bg-slate-100 dark:bg-[#181818]">
        <div id="heatmapMap" class="w-full h-full"></div>

        <!-- Overlay Loading -->
        <div id="heatmapLoading" class="absolute inset-0 bg-white/70 dark:bg-[#141414]/80 backdrop-blur-sm z-20 flex flex-col items-center justify-center space-y-2">
            <div class="w-8 h-8 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-xs font-bold text-slate-700 dark:text-[#CCCCCC]">Mengambil data titik sebaran OpenFreeMap...</span>
        </div>

        <!-- Legend Panas & Kategori Icon (Pojok Kiri Bawah) -->
        <div class="absolute bottom-6 left-6 z-10 bg-white/95 dark:bg-[#141414]/95 backdrop-blur-md p-3.5 rounded-2xl border border-slate-200/80 dark:border-[#282828] shadow-lg text-xs space-y-3 max-w-[280px] sm:max-w-xs">
            <!-- Heatmap Gradient -->
            <div>
                <div class="font-extrabold text-slate-900 dark:text-[#EDEDEC] text-xs mb-1">Intensitas Panas Masalah</div>
                <div class="h-2.5 w-full rounded-full bg-gradient-to-r from-blue-400 via-yellow-400 via-orange-500 to-rose-600 shadow-inner"></div>
                <div class="flex justify-between text-[10px] font-bold text-slate-500 dark:text-[#888888] mt-1">
                    <span>Rendah (0-9)</span>
                    <span>Sedang (10-49)</span>
                    <span>Kritis (100+)</span>
                </div>
            </div>

            <div class="border-t border-slate-200/80 dark:border-[#262626]"></div>

            <!-- Category Icons Legend -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="font-extrabold text-slate-900 dark:text-[#EDEDEC] text-xs">Icon Kategori Pada Peta</span>
                    <span class="text-[9px] font-semibold text-slate-400 bg-slate-100 dark:bg-[#202020] px-1.5 py-0.5 rounded">Zoom &ge; 12</span>
                </div>
                <div class="grid grid-cols-2 gap-x-2 gap-y-2 text-[11px]">
                    @foreach($categories as $key => $cat)
                        <div class="flex items-center gap-1.5 text-slate-700 dark:text-[#CCCCCC]">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center shrink-0" style="background-color: {{ $cat['color'] }}20; color: {{ $cat['color'] }};">
                                <flux:icon name="{{ $cat['icon'] }}" class="w-3 h-3" />
                            </span>
                            <span class="truncate text-[10px] font-medium">{{ $cat['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Banner Fokus Titik Laporan Terpilih -->
        <div id="focusReportBanner" class="hidden absolute top-4 left-14 sm:left-16 z-10 font-sans text-xs">
            <div class="flex items-center gap-2 bg-white/95 dark:bg-[#161615]/95 backdrop-blur-md rounded-2xl border border-rose-200 dark:border-rose-900/60 shadow-lg px-3 py-2">
                <span class="relative flex h-2.5 w-2.5 shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                </span>
                <span class="font-bold text-slate-900 dark:text-[#EDEDEC] truncate max-w-[140px] sm:max-w-xs" id="focusReportTitle">
                    Fokus Titik Laporan
                </span>
                <button type="button" id="btnResetFocus" title="Tampilkan Seluruh Titik Sebaran" class="ml-1 text-[10px] font-bold text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-[#252525] dark:hover:bg-[#303030] transition cursor-pointer shrink-0">
                    Lihat Semua
                </button>
            </div>
        </div>

        <!-- Kontrol Pencarian Lokasi & GPS (Pojok Kanan Atas) -->
        <div class="absolute top-4 right-4 left-14 sm:left-auto sm:w-96 max-w-[calc(100%-4.5rem)] z-10 font-sans text-xs">
            <div class="relative flex items-center bg-white/95 dark:bg-[#161615]/95 backdrop-blur-md rounded-2xl border border-slate-200 dark:border-[#282828] shadow-lg p-1.5 gap-1.5 transition-all focus-within:ring-2 focus-within:ring-emerald-500/30 dark:focus-within:ring-emerald-400/20">
                <!-- Search Input Group -->
                <div class="flex items-center flex-1 min-w-0 pl-2.5 pr-1 gap-2">
                    <flux:icon name="magnifying-glass" class="w-4 h-4 text-slate-400 dark:text-slate-500 shrink-0" />
                    <input
                        type="text"
                        id="locationSearchInput"
                        placeholder="Cari kota, kecamatan, jalan..."
                        autocomplete="off"
                        class="w-full bg-transparent text-xs text-slate-900 dark:text-[#EDEDEC] placeholder-slate-400 dark:placeholder-slate-500 focus:outline-hidden py-1"
                    >
                    <!-- Clear Button -->
                    <button type="button" id="clearSearchBtn" title="Hapus pencarian" class="hidden p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-md cursor-pointer transition">
                        <flux:icon name="x-mark" class="w-3.5 h-3.5" />
                    </button>
                    <!-- Search Spinner -->
                    <div id="searchSpinner" class="hidden w-3.5 h-3.5 border-2 border-emerald-600 dark:border-emerald-400 border-t-transparent rounded-full animate-spin shrink-0"></div>
                </div>

                <!-- Separator -->
                <div class="w-px h-5 bg-slate-200 dark:bg-[#282828] shrink-0"></div>

                <!-- GPS Button -->
                <button
                    type="button"
                    id="btnMyLoc"
                    title="Pusatkan ke Lokasi GPS Saya"
                    class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-[#222222] dark:hover:bg-[#2A2A2A] text-slate-800 dark:text-[#EDEDEC] text-xs font-semibold shrink-0 cursor-pointer transition active:scale-95"
                >
                    <flux:icon id="gpsIcon" name="viewfinder-circle" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" />
                    <div id="gpsLoading" class="hidden w-3.5 h-3.5 border-2 border-emerald-600 dark:border-emerald-400 border-t-transparent rounded-full animate-spin shrink-0"></div>
                    <span id="gpsText" class="hidden sm:inline">GPS Saya</span>
                </button>
            </div>

            <!-- Dropdown Hasil Pencarian -->
            <div
                id="searchResultsDropdown"
                class="hidden absolute top-full left-0 right-0 mt-1.5 bg-white dark:bg-[#161615] border border-slate-200 dark:border-[#282828] rounded-2xl shadow-2xl max-h-64 overflow-y-auto z-30 divide-y divide-slate-100 dark:divide-[#222222] overscroll-contain font-sans"
            >
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const urlParams = new URLSearchParams(window.location.search);
    const targetLat = parseFloat(urlParams.get('lat'));
    const targetLng = parseFloat(urlParams.get('lng'));
    const targetReportId = parseInt(urlParams.get('report_id'), 10);
    const hasTargetCoords = !isNaN(targetLat) && !isNaN(targetLng);

    const map = new maplibregl.Map({
        container: 'heatmapMap',
        style: 'https://tiles.openfreemap.org/styles/bright',
        center: hasTargetCoords ? [targetLng, targetLat] : [107.609810, -6.914744], // Default fokus (Bandung / Indonesia) atau koordinat target
        zoom: hasTargetCoords ? 16 : 12
    });

    map.addControl(new maplibregl.NavigationControl(), 'top-left');

    // Helper membuat SVG circular badge untuk icon kategori visual
    function createCategorySvg(color, innerSvg) {
        return `
            <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44">
                <defs>
                    <filter id="shadow" x="-25%" y="-25%" width="150%" height="150%">
                        <feDropShadow dx="0" dy="2" stdDeviation="2.5" flood-color="#000000" flood-opacity="0.4"/>
                    </filter>
                </defs>
                <circle cx="22" cy="22" r="18" fill="${color}" stroke="#ffffff" stroke-width="2.5" filter="url(#shadow)"/>
                <g transform="translate(12, 12)">
                    ${innerSvg}
                </g>
            </svg>
        `.trim();
    }

    // Registrasi 7 icon kategori ke dalam MapLibre GL
    function registerCategoryIcons(mapInstance) {
        const categorySvgs = {
            'cat-icon-kebakaran': createCategorySvg('#ef4444', '<path d="M10 1.5c-.6 1.5-1.2 2.9-1.1 4.4.1 1.8 1.3 3.3 1.2 5.1-.1 1.5-1 2.7-2 3.6-1.2-1.1-1.7-2.6-1.6-4.1 0-.4 0-.9.1-1.3C5 10.5 3.6 12.5 3.6 14.8c0 3.2 2.9 5.8 6.4 5.8s6.4-2.6 6.4-5.8c0-3.5-2.7-5.8-3.7-9-.6 1.2-1.3 2.3-1.6 3.6-.4-1.9.1-3.9-.9-5.7-.1-.7-.2-1.5-.2-2.2z" fill="#ffffff"/>'),
            'cat-icon-infrastruktur': createCategorySvg('#f97316', '<path d="M17.4 5.6a5.5 5.5 0 0 0-7.3 7.3L3.3 19.7a1.5 1.5 0 0 0 2.1 2.1l6.8-6.8a5.5 5.5 0 0 0 7.3-7.3l-2.7 2.7-2.1-2.1 2.7-2.7z" fill="#ffffff"/>'),
            'cat-icon-bencana_alam': createCategorySvg('#0284c7', '<path d="M10 2s-4 4-4 6.5c0 2.2 1.8 4 4 4s4-1.8 4-4c0-2.5-4-6.5-4-6.5z" fill="#ffffff"/><path d="M2 14c1.5 0 2.5-1 4-1s2.5 1 4 1 2.5-1 4-1 2.5 1 4 1v2c-1.5 0-2.5-1-4-1s-2.5 1-4 1-2.5-1-4-1-2.5 1-4 1v-2z" fill="#ffffff"/><path d="M2 17c1.5 0 2.5-1 4-1s2.5 1 4 1 2.5-1 4-1 2.5 1 4 1v2c-1.5 0-2.5-1-4-1s-2.5 1-4 1-2.5-1-4-1-2.5 1-4 1v-2z" fill="#ffffff"/>'),
            'cat-icon-kelistrikan': createCategorySvg('#eab308', '<path d="M11.5 1L3 11.5h6l-2.5 7.5 9.5-10.5h-6l2.5-7.5z" fill="#ffffff"/>'),
            'cat-icon-lingkungan': createCategorySvg('#16a34a', '<path d="M4 6h12M7.5 6V4a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v2M5.5 6v10.5a2 2 0 0 0 2 2h5a2 2 0 0 0 2-2V6M8 9.5v5.5M12 9.5v5.5" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>'),
            'cat-icon-fasilitas_umum': createCategorySvg('#8b5cf6', '<path d="M3.5 18V3a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v15M1.5 18h17M6.5 6h2M11.5 6h2M6.5 10h2M11.5 10h2M6.5 14h2M11.5 14h2M8.5 18v-3h3v3" stroke="#ffffff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" fill="none"/>'),
            'cat-icon-lainnya': createCategorySvg('#64748b', '<path d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16zM10 9v5M10 6h.01" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>'),
        };

        const promises = Object.entries(categorySvgs).map(([id, svg]) => {
            return new Promise((resolve) => {
                const img = new Image(44, 44);
                img.onload = () => {
                    if (!mapInstance.hasImage(id)) {
                        mapInstance.addImage(id, img);
                    }
                    resolve();
                };
                img.onerror = () => resolve();
                img.src = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg);
            });
        });

        return Promise.all(promises);
    }

    let cachedGeoJson = null;

    map.on('load', async () => {
        const loading = document.getElementById('heatmapLoading');

        // Preload visual SVG category icons
        await registerCategoryIcons(map);

        // Tambahkan GeoJSON source dari API Laravel
        map.addSource('reports-data', {
            type: 'geojson',
            data: "{{ route('api.reports.heatmap') }}"
        });

        // 1. Layer Heatmap (aktif pada zoom rendah sampai sedang)
        map.addLayer({
            id: 'reports-heat',
            type: 'heatmap',
            source: 'reports-data',
            maxzoom: 17,
            paint: {
                // Bobot vote_score diambil dari properties.weight
                'heatmap-weight': [
                    'interpolate',
                    ['linear'],
                    ['get', 'weight'],
                    0, 0,
                    10, 0.4,
                    50, 0.8,
                    100, 1.5
                ],
                'heatmap-intensity': [
                    'interpolate',
                    ['linear'],
                    ['zoom'],
                    0, 1,
                    12, 2.5,
                    16, 4
                ],
                // Palet warna panas (Biru -> Hijau Muda -> Kuning -> Oranye -> Merah Membara)
                'heatmap-color': [
                    'interpolate',
                    ['linear'],
                    ['heatmap-density'],
                    0, 'rgba(0, 0, 0, 0)',
                    0.2, 'rgb(65, 182, 196)',
                    0.4, 'rgb(127, 205, 187)',
                    0.6, 'rgb(254, 217, 118)',
                    0.8, 'rgb(254, 153, 41)',
                    1, 'rgb(227, 26, 28)'
                ],
                'heatmap-radius': [
                    'interpolate',
                    ['linear'],
                    ['zoom'],
                    0, 4,
                    10, 18,
                    15, 30
                ],
                'heatmap-opacity': [
                    'interpolate',
                    ['linear'],
                    ['zoom'],
                    14, 0.9,
                    17, 0.3
                ]
            }
        });

        // 2. Layer Halo Lingkaran Titik (Mulai muncul saat zoom mendekat >= 12)
        map.addLayer({
            id: 'reports-point',
            type: 'circle',
            source: 'reports-data',
            minzoom: 12,
            paint: {
                'circle-radius': [
                    'interpolate',
                    ['linear'],
                    ['zoom'],
                    12, 10,
                    16, 16
                ],
                'circle-color': [
                    'match',
                    ['get', 'category'],
                    'kebakaran', '#ef4444',
                    'infrastruktur', '#f97316',
                    'bencana_alam', '#0284c7',
                    'kelistrikan', '#eab308',
                    'lingkungan', '#16a34a',
                    'fasilitas_umum', '#8b5cf6',
                    /* lainnya */ '#64748b'
                ],
                'circle-opacity': 0.25,
                'circle-stroke-width': 1.5,
                'circle-stroke-color': [
                    'match',
                    ['get', 'category'],
                    'kebakaran', '#ef4444',
                    'infrastruktur', '#f97316',
                    'bencana_alam', '#0284c7',
                    'kelistrikan', '#eab308',
                    'lingkungan', '#16a34a',
                    'fasilitas_umum', '#8b5cf6',
                    /* lainnya */ '#64748b'
                ]
            }
        });

        // 3. Layer Icon Kategori Visual (Symbol layer dengan icon badge kategori)
        map.addLayer({
            id: 'reports-icons',
            type: 'symbol',
            source: 'reports-data',
            minzoom: 12,
            layout: {
                'icon-image': ['get', 'category_icon_id'],
                'icon-size': [
                    'interpolate',
                    ['linear'],
                    ['zoom'],
                    12, 0.65,
                    14, 0.85,
                    16, 1.05,
                    18, 1.25
                ],
                'icon-allow-overlap': true,
                'icon-ignore-placement': true
            }
        });

        // Zoom fit bounds jika ada data awal, atau sorot target laporan jika ada koordinat di URL
        fetch("{{ route('api.reports.heatmap') }}")
            .then(res => res.json())
            .then(geojson => {
                if (loading) loading.style.display = 'none';
                cachedGeoJson = geojson;

                if (hasTargetCoords) {
                    highlightTargetReport(targetLat, targetLng, targetReportId, geojson);
                } else if (geojson.features && geojson.features.length > 0) {
                    const bounds = new maplibregl.LngLatBounds();
                    geojson.features.forEach(f => {
                        bounds.extend(f.geometry.coordinates);
                    });
                    map.fitBounds(bounds, { padding: 80, maxZoom: 14 });
                }
            })
            .catch(() => {
                if (loading) loading.style.display = 'none';
            });

        // SVG Icon Flux untuk Popup Informasi
        const fluxIconsSvg = {
            'fire': '<svg class="w-3 h-3 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z"/></svg>',
            'wrench': '<svg class="w-3 h-3 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75a4.5 4.5 0 0 1-4.884 4.484c-1.076-.091-2.264.071-2.95.904l-7.152 8.684a2.548 2.548 0 1 1-3.586-3.586l8.684-7.152c.833-.686.995-1.874.904-2.95a4.5 4.5 0 0 1 6.336-4.486l-3.276 3.276a3.004 3.004 0 0 0 2.25 2.25l3.276-3.276c.256.565.398 1.192.398 1.852Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M4.867 19.125h.008v.008h-.008v-.008Z"/></svg>',
            'cloud': '<svg class="w-3 h-3 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 0 0 4.5 4.5H18a3.75 3.75 0 0 0 1.332-7.257 3 3 0 0 0-3.758-3.848 5.25 5.25 0 0 0-10.233 2.33A4.502 4.502 0 0 0 2.25 15Z"/></svg>',
            'bolt': '<svg class="w-3 h-3 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"/></svg>',
            'trash': '<svg class="w-3 h-3 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>',
            'building-office': '<svg class="w-3 h-3 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>',
            'tag': '<svg class="w-3 h-3 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/></svg>'
        };

        // Handler Popup Klik pada titik laporan / icon
        function handleFeatureClick(e) {
            if (!e.features || e.features.length === 0) return;
            const coordinates = e.features[0].geometry.coordinates.slice();
            const props = e.features[0].properties;

            const iconSvg = fluxIconsSvg[props.category_icon] || fluxIconsSvg['tag'];
            const categoryBadge = `
                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-full ${props.category_badge_class || 'bg-slate-100 text-slate-800'}">
                    ${iconSvg}
                    <span>${props.category_label || 'Laporan'}</span>
                </span>
            `;

            const popupContent = `
                <div class="p-2 space-y-2 font-sans max-w-[260px]">
                    <div class="flex items-center justify-between gap-1.5 flex-wrap">
                        ${categoryBadge}
                        <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">
                            ${props.rank_tier} TIER &bull; ${props.vote_score} Votes
                        </span>
                    </div>
                    <div class="text-xs font-bold text-slate-900 leading-snug">${escapeHtml(props.title)}</div>
                    <div class="text-[11px] text-slate-500 leading-tight">
                        <div class="font-medium">${props.district ? escapeHtml(props.district) + ', ' : ''}${escapeHtml(props.city || '')}</div>
                        <div class="text-[10px] text-slate-400 mt-0.5">${escapeHtml(props.date || '')}</div>
                    </div>
                    <a href="${props.url}" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline pt-1">
                        Buka Detail Laporan &rarr;
                    </a>
                </div>
            `;

            new maplibregl.Popup({ offset: 14 })
                .setLngLat(coordinates)
                .setHTML(popupContent)
                .addTo(map);
        }

        map.on('click', 'reports-icons', handleFeatureClick);
        map.on('click', 'reports-point', handleFeatureClick);

        // -------------------------------------------------------------
        // Penyorotan Titik Laporan Tertarget (Focus Report Coordinate)
        // -------------------------------------------------------------
        let focusMarker = null;
        let focusPopup = null;

        function highlightTargetReport(lat, lng, reportId, geojson) {
            if (isNaN(lat) || isNaN(lng)) return;

            const banner = document.getElementById('focusReportBanner');
            const titleEl = document.getElementById('focusReportTitle');

            let matched = null;
            if (geojson && geojson.features && geojson.features.length > 0) {
                matched = geojson.features.find(f => {
                    if (reportId && f.properties && f.properties.id === reportId) return true;
                    const fLng = f.geometry.coordinates[0];
                    const fLat = f.geometry.coordinates[1];
                    return Math.abs(fLat - lat) < 0.00015 && Math.abs(fLng - lng) < 0.00015;
                });
            }

            map.flyTo({
                center: [lng, lat],
                zoom: 16,
                essential: true
            });

            if (focusMarker) focusMarker.remove();
            if (focusPopup) focusPopup.remove();

            // Marker Radar Berkedip (Pulsing Rose Radar)
            const markerEl = document.createElement('div');
            markerEl.className = 'relative flex items-center justify-center cursor-pointer';
            markerEl.innerHTML = `
                <span class="animate-ping absolute inline-flex h-10 w-10 rounded-full bg-rose-500 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-5 w-5 bg-rose-600 border-2 border-white shadow-xl"></span>
            `;

            let popupContent = '';
            if (matched) {
                const props = matched.properties;
                const iconSvg = fluxIconsSvg[props.category_icon] || fluxIconsSvg['tag'];
                const categoryBadge = `
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-full ${props.category_badge_class || 'bg-slate-100 text-slate-800'}">
                        ${iconSvg}
                        <span>${props.category_label || 'Laporan'}</span>
                    </span>
                `;

                popupContent = `
                    <div class="p-2 space-y-2 font-sans max-w-[260px]">
                        <div class="flex items-center justify-between gap-1.5 flex-wrap">
                            ${categoryBadge}
                            <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">
                                ${props.rank_tier} TIER &bull; ${props.vote_score} Votes
                            </span>
                        </div>
                        <div class="text-xs font-bold text-slate-900 leading-snug">${escapeHtml(props.title)}</div>
                        <div class="text-[11px] text-slate-500 leading-tight">
                            <div class="font-medium">${props.district ? escapeHtml(props.district) + ', ' : ''}${escapeHtml(props.city || '')}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">${escapeHtml(props.date || '')}</div>
                        </div>
                        <a href="${props.url}" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline pt-1">
                            Buka Detail Laporan &rarr;
                        </a>
                    </div>
                `;
                if (banner && titleEl) {
                    titleEl.textContent = 'Fokus: ' + props.title;
                    banner.classList.remove('hidden');
                }
            } else {
                popupContent = `
                    <div class="p-2 space-y-1 font-sans">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-rose-600">Titik Koordinat Laporan</div>
                        <div class="text-xs font-bold text-slate-900">Fokus Koordinat Terpilih</div>
                        <div class="text-[11px] text-slate-500 font-mono">${lat.toFixed(6)}, ${lng.toFixed(6)}</div>
                    </div>
                `;
                if (banner && titleEl) {
                    titleEl.textContent = 'Fokus: ' + lat.toFixed(5) + ', ' + lng.toFixed(5);
                    banner.classList.remove('hidden');
                }
            }

            focusPopup = new maplibregl.Popup({ offset: 16 })
                .setLngLat([lng, lat])
                .setHTML(popupContent)
                .addTo(map);

            focusMarker = new maplibregl.Marker({ element: markerEl })
                .setLngLat([lng, lat])
                .setPopup(focusPopup)
                .addTo(map);
        }

        const btnResetFocus = document.getElementById('btnResetFocus');
        if (btnResetFocus) {
            btnResetFocus.addEventListener('click', () => {
                if (focusMarker) focusMarker.remove();
                if (focusPopup) focusPopup.remove();
                const banner = document.getElementById('focusReportBanner');
                if (banner) banner.classList.add('hidden');

                const cleanUrl = window.location.pathname;
                window.history.replaceState({}, '', cleanUrl);

                if (cachedGeoJson && cachedGeoJson.features && cachedGeoJson.features.length > 0) {
                    const bounds = new maplibregl.LngLatBounds();
                    cachedGeoJson.features.forEach(f => bounds.extend(f.geometry.coordinates));
                    map.fitBounds(bounds, { padding: 80, maxZoom: 14 });
                }
            });
        }

        // Ubah kursor saat hover pada titik / icon
        ['reports-icons', 'reports-point'].forEach(layer => {
            map.on('mouseenter', layer, () => {
                map.getCanvas().style.cursor = 'pointer';
            });
            map.on('mouseleave', layer, () => {
                map.getCanvas().style.cursor = '';
            });
        });

        // -------------------------------------------------------------
        // Logika Filter Kategori Interaktif
        // -------------------------------------------------------------
        const categoryButtons = document.querySelectorAll('.cat-filter-btn');
        const activeFilterBadge = document.getElementById('activeFilterBadge');

        const categoryLabels = {
            'all': 'Semua Kategori ({{ $totalReports }})',
            @foreach($categories as $k => $c)
                '{{ $k }}': '{{ $c['label'] }} ({{ $categoryCounts[$k] ?? 0 }})',
            @endforeach
        };

        function applyCategoryFilter(cat) {
            // Update tampilan tombol aktif
            categoryButtons.forEach(btn => {
                const bCat = btn.getAttribute('data-category');
                if (bCat === cat) {
                    btn.className = 'cat-filter-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer bg-slate-900 text-white dark:bg-emerald-500 dark:text-slate-950 shadow-xs ring-2 ring-slate-900/20 dark:ring-emerald-400/30';
                } else {
                    btn.className = 'cat-filter-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer border border-slate-200 dark:border-[#282828] bg-slate-50 hover:bg-slate-100 dark:bg-[#1C1C1C] dark:hover:bg-[#252525] text-slate-700 dark:text-[#CCCCCC]';
                }
            });

            if (activeFilterBadge) {
                activeFilterBadge.textContent = categoryLabels[cat] || cat;
            }

            // Terapkan filter ekspresi MapLibre secara instan
            const layerFilter = cat === 'all' ? null : ['==', ['get', 'category'], cat];

            if (map.getLayer('reports-heat')) map.setFilter('reports-heat', layerFilter);
            if (map.getLayer('reports-point')) map.setFilter('reports-point', layerFilter);
            if (map.getLayer('reports-icons')) map.setFilter('reports-icons', layerFilter);

            // Pusatkan peta ke sebaran titik kategori yang dipilih jika ada
            if (cachedGeoJson && cachedGeoJson.features && cachedGeoJson.features.length > 0) {
                const matching = cachedGeoJson.features.filter(f => cat === 'all' || f.properties.category === cat);
                if (matching.length > 0) {
                    const bounds = new maplibregl.LngLatBounds();
                    matching.forEach(f => bounds.extend(f.geometry.coordinates));
                    map.fitBounds(bounds, { padding: 80, maxZoom: 14 });
                }
            }
        }

        categoryButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const cat = btn.getAttribute('data-category');
                applyCategoryFilter(cat);
            });
        });
    });

    // -------------------------------------------------------------
    // Kontrol Pencarian Lokasi & GPS Pengguna
    // -------------------------------------------------------------
    const searchInput = document.getElementById('locationSearchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const searchSpinner = document.getElementById('searchSpinner');
    const searchDropdown = document.getElementById('searchResultsDropdown');

    const btnMyLoc = document.getElementById('btnMyLoc');
    const gpsIcon = document.getElementById('gpsIcon');
    const gpsLoading = document.getElementById('gpsLoading');
    const gpsText = document.getElementById('gpsText');

    let searchMarker = null;
    let gpsMarker = null;
    let searchDebounceTimer = null;
    let activeResultIndex = -1;

    function closeSearchDropdown() {
        searchDropdown.classList.add('hidden');
        searchDropdown.innerHTML = '';
        activeResultIndex = -1;
    }

    function selectSearchResult(item) {
        if (!item || isNaN(item.lat) || isNaN(item.lng)) return;

        closeSearchDropdown();
        searchInput.value = item.name;
        clearSearchBtn.classList.remove('hidden');

        // Pusatkan peta ke lokasi yang dipilih
        map.flyTo({
            center: [item.lng, item.lat],
            zoom: 14,
            essential: true
        });

        // Pasang marker titik pencarian
        if (searchMarker) {
            searchMarker.remove();
        }

        const popup = new maplibregl.Popup({ offset: 25 })
            .setHTML(`
                <div class="p-1 space-y-1 font-sans">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Hasil Pencarian</div>
                    <div class="text-xs font-bold text-slate-900 leading-tight">${escapeHtml(item.name)}</div>
                    <div class="text-[11px] text-slate-500 leading-snug">${escapeHtml(item.display_name)}</div>
                </div>
            `);

        searchMarker = new maplibregl.Marker({ color: '#059669' })
            .setLngLat([item.lng, item.lat])
            .setPopup(popup)
            .addTo(map);

        popup.addTo(map);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function executeLocationSearch(query) {
        if (!query || query.trim().length < 2) {
            closeSearchDropdown();
            return;
        }

        if (searchSpinner) searchSpinner.classList.remove('hidden');

        fetch(`{{ route('api.geocode.search') }}?q=${encodeURIComponent(query.trim())}`)
            .then(res => res.json())
            .then(results => {
                if (searchSpinner) searchSpinner.classList.add('hidden');

                if (!results || results.length === 0) {
                    searchDropdown.innerHTML = `
                        <div class="p-3 text-center text-xs text-slate-500 dark:text-[#888888]">
                            Lokasi tidak ditemukan. Coba gunakan nama kota, jalan, atau daerah lain.
                        </div>
                    `;
                    searchDropdown.classList.remove('hidden');
                    return;
                }

                searchDropdown.innerHTML = '';
                results.forEach((item, index) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.setAttribute('data-index', index);
                    btn.className = 'w-full text-left p-2.5 sm:p-3 hover:bg-slate-50 dark:hover:bg-[#1E1E1E] transition flex items-start gap-2.5 cursor-pointer search-item-btn';
                    btn.innerHTML = `
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 21s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 7.2c0 7.3-8 11.8-8 11.8z" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-bold text-slate-900 dark:text-[#EDEDEC] truncate">${escapeHtml(item.name)}</div>
                            <div class="text-[11px] text-slate-500 dark:text-[#888888] truncate">${escapeHtml(item.display_name)}</div>
                        </div>
                    `;
                    btn.addEventListener('click', () => selectSearchResult(item));
                    searchDropdown.appendChild(btn);
                });

                searchDropdown.classList.remove('hidden');
            })
            .catch(err => {
                console.error('Pencarian lokasi gagal:', err);
                if (searchSpinner) searchSpinner.classList.add('hidden');
            });
    }

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const val = e.target.value;
            if (val.length > 0) {
                clearSearchBtn.classList.remove('hidden');
            } else {
                clearSearchBtn.classList.add('hidden');
            }

            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(() => {
                executeLocationSearch(val);
            }, 300);
        });

        searchInput.addEventListener('keydown', (e) => {
            const items = searchDropdown.querySelectorAll('.search-item-btn');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (items.length > 0) {
                    activeResultIndex = (activeResultIndex + 1) % items.length;
                    items[activeResultIndex].focus();
                }
            } else if (e.key === 'Escape') {
                closeSearchDropdown();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (items.length > 0) {
                    items[0].click();
                } else if (searchInput.value.trim().length >= 2) {
                    executeLocationSearch(searchInput.value.trim());
                }
            }
        });
    }

    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', () => {
            searchInput.value = '';
            clearSearchBtn.classList.add('hidden');
            closeSearchDropdown();
            if (searchMarker) {
                searchMarker.remove();
                searchMarker = null;
            }
            searchInput.focus();
        });
    }

    // Klik di luar dropdown untuk menutup
    document.addEventListener('click', (e) => {
        if (searchDropdown && !searchDropdown.contains(e.target) && searchInput && !searchInput.contains(e.target)) {
            closeSearchDropdown();
        }
    });

    // Kontrol Pusatkan Lokasi GPS Pengguna
    if (btnMyLoc) {
        btnMyLoc.addEventListener('click', () => {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung fitur deteksi lokasi GPS.');
                return;
            }

            btnMyLoc.disabled = true;
            if (gpsIcon) gpsIcon.classList.add('hidden');
            if (gpsLoading) gpsLoading.classList.remove('hidden');
            if (gpsText) gpsText.textContent = 'Mendeteksi...';

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    btnMyLoc.disabled = false;
                    if (gpsIcon) gpsIcon.classList.remove('hidden');
                    if (gpsLoading) gpsLoading.classList.add('hidden');
                    if (gpsText) gpsText.textContent = 'GPS Saya';

                    const lng = pos.coords.longitude;
                    const lat = pos.coords.latitude;
                    const accuracy = Math.round(pos.coords.accuracy || 0);

                    map.flyTo({
                        center: [lng, lat],
                        zoom: 15,
                        essential: true
                    });

                    if (gpsMarker) {
                        gpsMarker.remove();
                    }

                    // Elemen kustom: Pulsing Blue Radar Marker
                    const markerEl = document.createElement('div');
                    markerEl.className = 'relative flex items-center justify-center';
                    markerEl.innerHTML = `
                        <span class="animate-ping absolute inline-flex h-8 w-8 rounded-full bg-blue-500 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-blue-600 border-2 border-white shadow-md"></span>
                    `;

                    const popup = new maplibregl.Popup({ offset: 15 })
                        .setHTML(`
                            <div class="p-1 space-y-0.5 font-sans">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-blue-600">Lokasi Anda</div>
                                <div class="text-xs font-bold text-slate-900 leading-tight">Posisi GPS Saat Ini</div>
                                <div class="text-[11px] text-slate-500 font-mono">Akurasi: &plusmn;${accuracy} meter</div>
                            </div>
                        `);

                    gpsMarker = new maplibregl.Marker({ element: markerEl })
                        .setLngLat([lng, lat])
                        .setPopup(popup)
                        .addTo(map);

                    popup.addTo(map);
                },
                (err) => {
                    btnMyLoc.disabled = false;
                    if (gpsIcon) gpsIcon.classList.remove('hidden');
                    if (gpsLoading) gpsLoading.classList.add('hidden');
                    if (gpsText) gpsText.textContent = 'GPS Saya';

                    let msg = 'Gagal mendeteksi lokasi GPS.';
                    if (err.code === 1) {
                        msg = 'Izin akses lokasi GPS ditolak oleh browser/pengguna.';
                    } else if (err.code === 2) {
                        msg = 'Informasi titik lokasi GPS tidak tersedia.';
                    } else if (err.code === 3) {
                        msg = 'Waktu permintaan deteksi GPS habis (timeout).';
                    }
                    alert(msg);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    }
</script>
@endpush
