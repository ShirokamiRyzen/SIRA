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

    <!-- Peta Heatmap Kontainer -->
    <div class="relative w-full h-[650px] rounded-3xl border border-slate-300 dark:border-[#282828] shadow-md overflow-hidden bg-slate-100 dark:bg-[#181818]">
        <div id="heatmapMap" class="w-full h-full"></div>

        <!-- Overlay Loading -->
        <div id="heatmapLoading" class="absolute inset-0 bg-white/70 dark:bg-[#141414]/80 backdrop-blur-sm z-20 flex flex-col items-center justify-center space-y-2">
            <div class="w-8 h-8 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-xs font-bold text-slate-700 dark:text-[#CCCCCC]">Mengambil data titik sebaran OpenFreeMap...</span>
        </div>

        <!-- Legend Panas (Pojok Kiri Bawah) -->
        <div class="absolute bottom-6 left-6 z-10 bg-white/90 dark:bg-[#141414]/90 backdrop-blur-md p-3.5 rounded-2xl border border-slate-200/80 dark:border-[#282828] shadow-md text-xs space-y-2 max-w-xs">
            <div class="font-extrabold text-slate-900 dark:text-[#EDEDEC]">Intensitas Panas Masalah</div>
            <div class="h-3 w-48 rounded-full bg-gradient-to-r from-blue-400 via-yellow-400 via-orange-500 to-rose-600 shadow-inner"></div>
            <div class="flex justify-between text-[10px] font-bold text-slate-500 dark:text-[#888888]">
                <span>Rendah (0-9)</span>
                <span>Sedang (10-49)</span>
                <span>Kritis (100+)</span>
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
    const map = new maplibregl.Map({
        container: 'heatmapMap',
        style: 'https://tiles.openfreemap.org/styles/bright',
        center: [107.609810, -6.914744], // Default fokus (Bandung / Indonesia)
        zoom: 12
    });

    map.addControl(new maplibregl.NavigationControl(), 'top-left');

    map.on('load', () => {
        const loading = document.getElementById('heatmapLoading');

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

        // 2. Layer Lingkaran Titik Interaktif (Mulai muncul saat zoom mendekat >= 13)
        map.addLayer({
            id: 'reports-point',
            type: 'circle',
            source: 'reports-data',
            minzoom: 13,
            paint: {
                'circle-radius': [
                    'interpolate',
                    ['linear'],
                    ['zoom'],
                    13, 5,
                    16, 9
                ],
                'circle-color': [
                    'match',
                    ['get', 'rank_tier'],
                    'critical', '#E11D48',
                    'urgent', '#F59E0B',
                    'trending', '#0D9488',
                    /* normal */ '#3B82F6'
                ],
                'circle-stroke-color': '#ffffff',
                'circle-stroke-width': 2,
                'circle-opacity': 0.95
            }
        });

        // Zoom fit bounds jika ada data
        fetch("{{ route('api.reports.heatmap') }}")
            .then(res => res.json())
            .then(geojson => {
                if (loading) loading.style.display = 'none';

                if (geojson.features && geojson.features.length > 0) {
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

        // Popup klik pada titik laporan
        map.on('click', 'reports-point', (e) => {
            const coordinates = e.features[0].geometry.coordinates.slice();
            const props = e.features[0].properties;

            const popupContent = `
                <div class="p-1 space-y-1.5 font-sans">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-rose-600">${props.rank_tier} TIER &bull; ${props.vote_score} Votes</div>
                    <div class="text-xs font-bold text-slate-900 leading-tight">${props.title}</div>
                    <div class="text-[11px] text-slate-500">${props.district ? props.district + ', ' : ''}${props.city || ''}</div>
                    <a href="${props.url}" class="inline-block mt-2 text-[11px] font-bold text-emerald-700 hover:underline">
                        Buka Laporan &rarr;
                    </a>
                </div>
            `;

            new maplibregl.Popup()
                .setLngLat(coordinates)
                .setHTML(popupContent)
                .addTo(map);
        });

        // Ubah kursor saat hover pada titik
        map.on('mouseenter', 'reports-point', () => {
            map.getCanvas().style.cursor = 'pointer';
        });
        map.on('mouseleave', 'reports-point', () => {
            map.getCanvas().style.cursor = '';
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
