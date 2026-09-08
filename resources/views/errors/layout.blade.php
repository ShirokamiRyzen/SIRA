<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Terjadi Kesalahan') — SIRA</title>
    <meta name="robots" content="noindex, follow">

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

    <!-- Tipografi: Plus Jakarta Sans & Geist Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400..700;1,400..700&family=Geist+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Script Tema Gelap/Terang Otomatis -->
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

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .font-mono {
            font-family: 'Geist Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
    </style>
</head>
<body class="bg-[#FBFBFA] dark:bg-[#0E0E0E] text-[#111111] dark:text-[#EDEDEC] min-h-screen flex flex-col antialiased selection:bg-[#EAEAEA] dark:selection:bg-[#2A2A2A]">
    <!-- Minimalist Header -->
    <header class="border-b border-slate-200/80 dark:border-[#262626] bg-white/80 dark:bg-[#141414]/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 group">
                <img src="{{ asset('android-chrome-512x512.png') }}" alt="SIRA Logo" class="w-7 h-7 rounded-[6px] object-contain shrink-0">
                <span class="font-bold text-sm tracking-tight text-[#111111] dark:text-[#EDEDEC] group-hover:opacity-80 transition-opacity">
                    SIRA
                </span>
                <span class="text-[10px] font-mono uppercase px-1.5 py-0.5 rounded bg-slate-100 dark:bg-[#202020] text-slate-500 dark:text-[#888888]">
                    Sistem Publik
                </span>
            </a>

            <div class="flex items-center gap-3">
                <button type="button" onclick="toggleTheme()" class="p-1.5 rounded-[6px] text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white border border-slate-200 dark:border-[#262626] hover:bg-slate-100 dark:hover:bg-[#202020] transition cursor-pointer" aria-label="Ganti Tema">
                    <svg class="w-4 h-4 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg class="w-4 h-4 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-lg">
            <div class="bg-white dark:bg-[#141414] border border-slate-200/90 dark:border-[#262626] rounded-xl p-6 sm:p-8 shadow-sm">
                <!-- Status Badge -->
                <div class="flex items-center justify-between mb-4">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[6px] font-mono text-xs font-semibold @yield('badge_class', 'bg-slate-100 dark:bg-[#202020] text-slate-700 dark:text-[#CCCCCC]')">
                        <span class="w-1.5 h-1.5 rounded-full @yield('dot_class', 'bg-slate-400')"></span>
                        HTTP @yield('code', 'ERROR')
                    </span>
                    <span class="font-mono text-[11px] text-slate-400 dark:text-[#777777]">
                        {{ now()->format('d/m/Y H:i') }} WIB
                    </span>
                </div>

                <!-- Headline -->
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-[#111111] dark:text-[#EDEDEC] mb-2">
                    @yield('title', 'Terjadi Masalah')
                </h1>

                <!-- Description -->
                <p class="text-sm text-slate-600 dark:text-[#A0A0A0] leading-relaxed mb-6">
                    @yield('message', 'Permintaan Anda saat ini tidak dapat diproses oleh server.')
                </p>

                <!-- Optional Details Block -->
                @hasSection('details')
                    <div class="mb-6 p-3 rounded-[6px] bg-slate-50 dark:bg-[#1A1A19] border border-slate-200/70 dark:border-[#282828] text-xs font-mono text-slate-600 dark:text-[#A0A0A0] overflow-x-auto">
                        @yield('details')
                    </div>
                @endif

                <!-- Navigation Actions -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 pt-2 border-t border-slate-100 dark:border-[#222222]">
                    <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-[6px] bg-[#111111] hover:bg-[#2B2B2B] dark:bg-[#EDEDEC] dark:hover:bg-white text-white dark:text-[#111111] text-xs font-semibold transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Kembali ke Beranda
                    </a>

                    <button type="button" onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ url('/') }}'" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-[6px] bg-slate-100 hover:bg-slate-200 dark:bg-[#1F1F1F] dark:hover:bg-[#282828] text-slate-700 dark:text-[#CCCCCC] text-xs font-medium border border-slate-200/80 dark:border-[#2E2E2E] transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Halaman Sebelumnya
                    </button>
                </div>
            </div>

            <!-- Helpful Meta Context -->
            <div class="mt-4 px-2 flex items-center justify-between text-[11px] font-mono text-slate-400 dark:text-[#666666]">
                <span>SIRA v1.0 &bull; GIS OpenMap</span>
                <a href="{{ route('reports.index') }}" class="hover:underline hover:text-slate-600 dark:hover:text-[#999999]">Daftar Pengaduan &rarr;</a>
            </div>
        </div>
    </main>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }
    </script>
</body>
</html>
