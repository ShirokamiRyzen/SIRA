<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIRA — Sistem Informasi Ruang Aman')</title>

    <!-- SEO Meta Tags Primer -->
    <meta name="description" content="@yield('meta_description', 'Platform pengaduan publik berbasis GIS OpenMap dengan algoritma prioritas ranking Wilson Score & keterlibatan warga secara transparan.')">
    <meta name="keywords" content="SIRA, sistem informasi ruang aman, pengaduan publik, lapor fasilitas, GIS openmap, partisipasi warga, transparansi fasilitas publik">
    <meta name="author" content="SIRA — Sistem Informasi Ruang Aman">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- Ikon Aplikasi & Favicon (Folder Public) -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('android-chrome-512x512.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#111111" media="(prefers-color-scheme: dark)">
    <meta name="theme-color" content="#FBFBFA" media="(prefers-color-scheme: light)">
    <meta name="application-name" content="SIRA">
    <meta name="apple-mobile-web-app-title" content="SIRA">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="mobile-web-app-capable" content="yes">

    <!-- OpenGraph & Twitter Card Dinamis -->
    <meta property="og:site_name" content="SIRA — Sistem Informasi Ruang Aman">
    <meta property="og:title" content="@yield('og_title', 'SIRA — Sistem Informasi Ruang Aman')">
    <meta property="og:description" content="@yield('og_description', 'Platform pengaduan publik berbasis GIS OpenMap dengan algoritma prioritas ranking Wilson Score & keterlibatan warga secara transparan.')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:image" content="@yield('og_image', route('og.default'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/png">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'SIRA — Sistem Informasi Ruang Aman')">
    <meta name="twitter:description" content="@yield('og_description', 'Platform pengaduan publik berbasis GIS OpenMap dengan algoritma prioritas ranking Wilson Score & keterlibatan warga secara transparan.')">
    <meta name="twitter:image" content="@yield('og_image', route('og.default'))">

    <!-- JSON-LD Structured Data Schema untuk SEO Mesin Pencari -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "WebSite",
        "name": "SIRA — Sistem Informasi Ruang Aman",
        "alternateName": "SIRA",
        "url": "{{ url('/') }}",
        "description": "Platform pengaduan publik berbasis GIS OpenMap dengan algoritma prioritas ranking Wilson Score & keterlibatan warga secara transparan.",
        "image": "{{ asset('android-chrome-512x512.png') }}"
    }
    </script>

    <!-- Tipografi: Plus Jakarta Sans & Geist Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&family=Geist+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Script Pencegah Flash Tema Gelap/Terang -->
    <script>
        (function () {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <!-- MapLibre GL JS untuk OpenFreeMap -->
    <link href="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css" rel="stylesheet" />
    <script src="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-sand-25 dark:bg-sand-1000 text-sand-975 dark:text-sand-100 antialiased min-h-screen flex flex-col font-sans selection:bg-sand-100 dark:selection:bg-sand-850">
    <!-- Navbar Header Component -->
    <x-header />

    <!-- Global Alerts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full font-mono text-xs">
        @if (session('success'))
            <div class="flex items-center justify-between p-3.5 mb-4 rounded-md bg-success-tint text-success dark:bg-success-deep dark:text-success-soft border border-success/20" role="alert">
                <div class="flex items-center space-x-2">
                    <span class="font-bold">[SUKSES]</span>
                    <span class="font-sans text-xs">{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-success dark:text-success-soft hover:opacity-75 text-sm">&times;</button>
            </div>
        @endif

        @if (session('error') || $errors->any())
            <div class="p-3.5 mb-4 rounded-md bg-danger-tint text-danger dark:bg-danger-deep dark:text-danger-soft border border-danger/20" role="alert">
                <div class="flex items-center space-x-2 font-bold mb-1">
                    <span>[PERINGATAN]</span>
                    <span class="font-sans text-xs">{{ session('error') ?? 'Terjadi kesalahan pada data input:' }}</span>
                </div>
                @if ($errors->any())
                    <ul class="list-disc list-inside text-[11px] space-y-0.5 text-danger dark:text-danger-soft">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif
    </div>

    <!-- Main Content Slot -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        @yield('content')
    </main>

    <!-- Footer Component -->
    <x-footer />

    <!-- Pop Up Poster Reward Bulanan -->
    <x-reward-modal />

    @livewireScripts

    <!-- Pengendali Pengalih Tema (Light/Dark Toggle Controller) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('theme-toggle');
            const lightIcon = document.getElementById('theme-toggle-light-icon');
            const darkIcon = document.getElementById('theme-toggle-dark-icon');
            const themeText = document.getElementById('theme-toggle-text');

            function syncThemeUI() {
                const isDark = document.documentElement.classList.contains('dark');
                if (isDark) {
                    if (lightIcon) lightIcon.classList.remove('hidden');
                    if (darkIcon) darkIcon.classList.add('hidden');
                    if (toggleBtn) toggleBtn.setAttribute('title', 'Beralih ke mode terang');
                } else {
                    if (lightIcon) lightIcon.classList.add('hidden');
                    if (darkIcon) darkIcon.classList.remove('hidden');
                    if (toggleBtn) toggleBtn.setAttribute('title', 'Beralih ke mode gelap');
                }
            }

            syncThemeUI();

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    const isDark = document.documentElement.classList.contains('dark');
                    if (isDark) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    }
                    syncThemeUI();
                });
            }
        });
    </script>

    <!-- Indikator Loading Global Saat Submit Form (Progress Bar Atas + Spinner Tombol) -->
    <style>
        @keyframes form-loading-slide {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(400%); }
        }
    </style>
    <div id="formLoadingBar" class="hidden fixed top-0 inset-x-0 h-0.5 z-[99999] overflow-hidden bg-emerald-500/20 pointer-events-none" aria-hidden="true">
        <div class="h-full w-1/4 bg-emerald-500" style="animation: form-loading-slide 1s ease-in-out infinite;"></div>
    </div>
    <script>
        (function () {
            const loadingBar = document.getElementById('formLoadingBar');
            const spinnerSvg = '<svg class="animate-spin h-3.5 w-3.5 shrink-0 inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>';
            const busyButtons = [];

            document.addEventListener('submit', function (e) {
                const form = e.target;
                if (form.target === '_blank' || form.hasAttribute('data-no-loading')) return;

                // Ditunda agar handler lain (validasi, confirm) selesai memutuskan preventDefault,
                // dan agar name/value tombol submitter sudah ikut terkirim sebelum tombol di-disable
                setTimeout(function () {
                    if (e.defaultPrevented) return;

                    if (loadingBar) loadingBar.classList.remove('hidden');

                    form.querySelectorAll('button[type="submit"], button:not([type]), input[type="submit"]').forEach(function (btn) {
                        if (btn.disabled) return;
                        busyButtons.push({ btn: btn, html: btn.innerHTML });
                        btn.disabled = true;
                        btn.setAttribute('aria-busy', 'true');
                        btn.classList.add('opacity-70', 'cursor-wait');
                        if (btn === e.submitter && btn.tagName === 'BUTTON') {
                            btn.insertAdjacentHTML('afterbegin', spinnerSvg + ' ');
                        }
                    });
                }, 0);
            });

            // Pulihkan tombol saat halaman dikembalikan dari back-forward cache
            window.addEventListener('pageshow', function (e) {
                if (!e.persisted) return;
                if (loadingBar) loadingBar.classList.add('hidden');
                busyButtons.splice(0).forEach(function (item) {
                    item.btn.disabled = false;
                    item.btn.removeAttribute('aria-busy');
                    item.btn.classList.remove('opacity-70', 'cursor-wait');
                    item.btn.innerHTML = item.html;
                });
            });
        })();
    </script>

    @stack('scripts')

    <!-- Wadah Toast Notifikasi Realtime Melayang -->
    <div id="realtimeNotificationToastContainer" class="fixed bottom-5 right-5 z-[99999] flex flex-col space-y-2 pointer-events-none max-w-sm w-full px-4 sm:px-0"></div>
</body>
</html>
