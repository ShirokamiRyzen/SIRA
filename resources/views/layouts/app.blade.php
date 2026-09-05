<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIRA - Sistem Informasi & Laporan Komunitas')</title>

    <!-- Tipografi: Plus Jakarta Sans, Geist Mono, Newsreader -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist+Mono:wght@400;500;600&family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;1,6..72,400;1,6..72,500&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

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
<body class="bg-[#FBFBFA] dark:bg-[#0E0E0E] text-[#111111] dark:text-[#EDEDEC] antialiased min-h-screen flex flex-col font-sans selection:bg-[#EAEAEA] dark:selection:bg-[#2A2A2A]">
    <!-- Navbar Header Component -->
    <x-header />

    <!-- Global Alerts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full font-mono text-xs">
        @if (session('success'))
            <div class="flex items-center justify-between p-3.5 mb-4 rounded-[6px] bg-[#EDF3EC] text-[#346538] dark:bg-[#1C281E] dark:text-[#82C78A] border border-[#346538]/20" role="alert">
                <div class="flex items-center space-x-2">
                    <span class="font-bold">[SUKSES]</span>
                    <span class="font-sans text-xs">{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-[#346538] dark:text-[#82C78A] hover:opacity-75 text-sm">&times;</button>
            </div>
        @endif

        @if (session('error') || $errors->any())
            <div class="p-3.5 mb-4 rounded-[6px] bg-[#FDEBEC] text-[#9F2F2D] dark:bg-[#311617] dark:text-[#E88C8A] border border-[#9F2F2D]/20" role="alert">
                <div class="flex items-center space-x-2 font-bold mb-1">
                    <span>[PERINGATAN]</span>
                    <span class="font-sans text-xs">{{ session('error') ?? 'Terjadi kesalahan pada data input:' }}</span>
                </div>
                @if ($errors->any())
                    <ul class="list-disc list-inside text-[11px] space-y-0.5 text-[#9F2F2D] dark:text-[#E88C8A]">
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
                    if (themeText) themeText.textContent = 'Terang';
                } else {
                    if (lightIcon) lightIcon.classList.add('hidden');
                    if (darkIcon) darkIcon.classList.remove('hidden');
                    if (themeText) themeText.textContent = 'Gelap';
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
    @stack('scripts')
</body>
</html>
