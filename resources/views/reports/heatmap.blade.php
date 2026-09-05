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

        <!-- Kontrol Lokasi Saya (Pojok Kanan Atas) -->
        <div class="absolute top-6 right-6 z-10">
            <button type="button" id="btnMyLoc" class="px-3.5 py-2 rounded-xl bg-white/90 hover:bg-white dark:bg-[#1E1E1E]/90 dark:hover:bg-[#252525] text-slate-800 dark:text-[#EDEDEC] font-bold text-xs shadow-md border border-slate-200 dark:border-[#333333] flex items-center space-x-1.5 backdrop-blur-md transition">
                <flux:icon name="viewfinder-circle" class="w-3.5 h-3.5 text-slate-700 dark:text-[#EDEDEC] shrink-0" />
                <span>Pusatkan Lokasi Saya</span>
            </button>
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

    // Kontrol Pusatkan Lokasi Pengguna
    document.getElementById('btnMyLoc').addEventListener('click', () => {
        if (!navigator.geolocation) {
            alert('Browser tidak mendukung geolokasi');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                map.flyTo({
                    center: [pos.coords.longitude, pos.coords.latitude],
                    zoom: 14,
                    essential: true
                });
            },
            (err) => {
                alert('Tidak dapat mendeteksi lokasi: ' + err.message);
            }
        );
    });
</script>
@endpush
