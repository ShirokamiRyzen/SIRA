<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'SIRA') }} &mdash; Application Console</title>

        <!-- Typography: Plus Jakarta Sans, Geist Mono, Newsreader -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Geist+Mono:wght@400;500;600&family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;1,6..72,400;1,6..72,500&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

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
        @livewireStyles
    </head>
    <body class="min-h-full bg-[#FBFBFA] dark:bg-[#0E0E0E] text-[#111111] dark:text-[#EDEDEC] font-sans antialiased selection:bg-[#EAEAEA] dark:selection:bg-[#2A2A2A] relative flex flex-col justify-between">
        <!-- Subtle Ambient Warm Depth -->
        <div class="fixed inset-0 pointer-events-none -z-10 overflow-hidden">
            <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[900px] h-[500px] rounded-full bg-amber-500/[0.025] blur-[100px] dark:bg-amber-400/[0.015]"></div>
        </div>

        <!-- Document Header / Navigation -->
        <header class="w-full border-b border-[#EAEAEA] dark:border-[#202020] bg-[#FBFBFA]/80 dark:bg-[#0E0E0E]/80 backdrop-blur-md sticky top-0 z-50">
            <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="/" class="flex items-center gap-2 group">
                        <span class="w-6 h-6 rounded-[4px] bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111] flex items-center justify-center font-mono text-xs font-semibold">
                            S
                        </span>
                        <span class="font-medium text-sm tracking-tight text-[#111111] dark:text-[#EDEDEC]">
                            {{ config('app.name', 'SIRA') }}
                        </span>
                    </a>
                    <span class="text-[#CCCCCC] dark:text-[#333333]">/</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#EDF3EC] text-[#346538] dark:bg-[#1C281E] dark:text-[#82C78A]">
                        Production Ready
                    </span>
                </div>

                <nav class="flex items-center gap-4 text-xs font-mono">
                    <a href="https://laravel.com/docs" target="_blank" rel="noopener" class="text-[#787774] hover:text-[#111111] dark:text-[#9B9B97] dark:hover:text-[#EDEDEC] transition-colors hidden sm:inline-block">
                        Docs
                    </a>
                    <a href="https://livewire.laravel.com/docs" target="_blank" rel="noopener" class="text-[#787774] hover:text-[#111111] dark:text-[#9B9B97] dark:hover:text-[#EDEDEC] transition-colors hidden sm:inline-block">
                        Livewire &amp; Volt
                    </a>

                    <!-- Theme Toggle Button (localStorage state) -->
                    <button
                        id="theme-toggle"
                        type="button"
                        aria-label="Toggle theme mode"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[4px] border border-[#EAEAEA] dark:border-[#262626] bg-white dark:bg-[#161615] text-[#111111] dark:text-[#EDEDEC] hover:bg-[#F7F6F3] dark:hover:bg-[#1F1F1E] transition-all active:scale-[0.98] font-mono text-[11px] cursor-pointer"
                    >
                        <svg id="theme-toggle-light-icon" class="w-3.5 h-3.5 hidden" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75">
                            <circle cx="8" cy="8" r="3"/>
                            <path d="M8 1.5v1.5M8 13v1.5M1.5 8h1.5M13 8h1.5M3.4 3.4l1.1 1.1M11.5 11.5l1.1 1.1M3.4 12.6l1.1-1.1M11.5 4.5l1.1-1.1" stroke-linecap="square"/>
                        </svg>
                        <svg id="theme-toggle-dark-icon" class="w-3.5 h-3.5 hidden" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75">
                            <path d="M13.5 9.8A6 6 0 1 1 6.2 2.5a4.8 4.8 0 0 0 7.3 7.3z" stroke-linecap="square"/>
                        </svg>
                        <span id="theme-toggle-text">Theme</span>
                    </button>

                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-3 py-1.5 rounded-[4px] bg-[#111111] text-white hover:bg-[#2A2A2A] dark:bg-[#EDEDEC] dark:text-[#111111] dark:hover:bg-[#FFFFFF] transition-all active:scale-[0.98]">
                                Dashboard &rarr;
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-[#787774] hover:text-[#111111] dark:text-[#9B9B97] dark:hover:text-[#EDEDEC] transition-colors">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-3 py-1.5 rounded-[4px] bg-[#111111] text-white hover:bg-[#2A2A2A] dark:bg-[#EDEDEC] dark:text-[#111111] dark:hover:bg-[#FFFFFF] transition-all active:scale-[0.98]">
                                    Register
                                </a>
                            @endif
                        @endauth
                    @endif
                </nav>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="w-full max-w-5xl mx-auto px-6 py-20 lg:py-24 grow flex flex-col justify-center">
            <!-- Hero Header Section -->
            <section class="max-w-3xl mb-16 lg:mb-20">
                <div class="inline-flex items-center gap-2 mb-6">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-mono tracking-[0.05em] uppercase bg-[#E1F3FE] text-[#1F6C9F] dark:bg-[#142634] dark:text-[#76BBE8]">
                        Technical Specification
                    </span>
                    <span class="text-xs font-mono text-[#787774] dark:text-[#8E8D8A]">
                        Laravel v{{ app()->version() }} &middot; PHP {{ PHP_VERSION }}
                    </span>
                </div>

                <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl text-[#111111] dark:text-[#F2F2F2] leading-[1.08] tracking-[-0.03em] font-normal mb-6">
                    Utilitarian simplicity for modern web applications.
                </h1>

                <p class="text-[#2F3437] dark:text-[#A1A09A] text-base sm:text-lg leading-[1.6] max-w-2xl mb-8">
                    An application scaffold built with precision engineering: unified reactivity with Livewire 4, declarative single-file Volt components, and instant compilation via Tailwind CSS v4.
                </p>

                <!-- Primary CTAs and Keystroke Micro-UI -->
                <div class="flex flex-wrap items-center gap-4">
                    <a href="https://laravel.com/docs" target="_blank" rel="noopener" class="px-5 py-2.5 bg-[#111111] text-white hover:bg-[#262626] dark:bg-[#EDEDEC] dark:text-[#111111] dark:hover:bg-white rounded-[5px] text-xs font-medium transition-all active:scale-[0.98] inline-flex items-center gap-2">
                        <span>Read Documentation</span>
                        <svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75">
                            <path d="M4 12L12 4M12 4H6M12 4V10" stroke-linecap="square"/>
                        </svg>
                    </a>

                    <a href="https://livewire.laravel.com/docs/volt" target="_blank" rel="noopener" class="px-5 py-2.5 bg-white dark:bg-[#161615] border border-[#EAEAEA] dark:border-[#262626] text-[#111111] dark:text-[#EDEDEC] hover:bg-[#F7F6F3] dark:hover:bg-[#1F1F1E] rounded-[5px] text-xs font-medium transition-all active:scale-[0.98] inline-flex items-center gap-2">
                        <span>Explore Volt Single-File</span>
                        <svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75">
                            <path d="M4 12L12 4M12 4H6M12 4V10" stroke-linecap="square"/>
                        </svg>
                    </a>

                    <div class="flex items-center gap-1.5 pl-2 py-1 text-xs font-mono text-[#787774] dark:text-[#8E8D8A]">
                        <span>Run:</span>
                        <kbd class="px-2 py-1 bg-[#F7F6F3] dark:bg-[#1C1C1B] border border-[#EAEAEA] dark:border-[#282828] rounded-[4px] text-[#111111] dark:text-[#EDEDED]">composer run dev</kbd>
                    </div>
                </div>
            </section>

            <!-- Bento Box Architecture Grid -->
            <section class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-20">
                <!-- Bento Cell 1: Reactive Telemetry (Volt Single File Component) -->
                <div class="md:col-span-7 flex flex-col">
                    <livewire:stack-status />
                </div>

                <!-- Bento Cell 2: Volt Single-File Paradigm -->
                <div class="md:col-span-5 border border-[#EAEAEA] dark:border-[#262626] rounded-[8px] bg-white dark:bg-[#161615] p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#E1F3FE] text-[#1F6C9F] dark:bg-[#142634] dark:text-[#76BBE8]">
                                Single-File Component
                            </span>
                            <span class="font-mono text-[11px] text-[#787774] dark:text-[#8E8D8A]">Volt Functional API</span>
                        </div>

                        <h2 class="font-serif text-xl text-[#111111] dark:text-[#EDEDEC] mb-2 font-normal">
                            Co-located logic and template
                        </h2>

                        <p class="text-xs text-[#787774] dark:text-[#9B9B97] leading-[1.6] mb-4">
                            Write entire reactive components in a single Blade view with zero boilerplate or extraneous controllers.
                        </p>

                        <!-- Code block preview -->
                        <div class="p-3.5 bg-[#FBFBFA] dark:bg-[#111111] border border-[#EAEAEA] dark:border-[#262626] rounded-[6px] font-mono text-[11px] leading-relaxed text-[#111111] dark:text-[#EDEDED] overflow-x-auto">
                            <span class="text-[#787774]">&lt;?php</span><br/>
                            <span class="text-[#1F6C9F] dark:text-[#76BBE8]">use function</span> Livewire\Volt\{state};<br/>
                            <br/>
                            state([<span class="text-[#346538] dark:text-[#82C78A]">'count'</span> =&gt; <span class="text-[#956400] dark:text-[#E0BE69]">0</span>]);<br/>
                            $increment = <span class="text-[#1F6C9F] dark:text-[#76BBE8]">fn</span>() =&gt; $this-&gt;count++;<br/>
                            <span class="text-[#787774]">?&gt;</span><br/>
                            <br/>
                            <span class="text-[#787774]">&lt;button wire:click="increment"&gt;</span><br/>
                            &nbsp;&nbsp;Count: &#123;&#123; $count &#125;&#125;<br/>
                            <span class="text-[#787774]">&lt;/button&gt;</span>
                        </div>
                    </div>

                    <div class="pt-4 mt-4 border-t border-[#EAEAEA] dark:border-[#262626] flex items-center justify-between text-[11px] font-mono text-[#787774] dark:text-[#8E8D8A]">
                        <span>Location: resources/views/livewire</span>
                        <a href="https://livewire.laravel.com/docs/volt" target="_blank" class="hover:text-[#111111] dark:hover:text-[#EDEDED] underline underline-offset-2">Manual &rarr;</a>
                    </div>
                </div>

                <!-- Bento Cell 3: Styling Engine -->
                <div class="md:col-span-4 border border-[#EAEAEA] dark:border-[#262626] rounded-[8px] bg-white dark:bg-[#161615] p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#EDF3EC] text-[#346538] dark:bg-[#1C281E] dark:text-[#82C78A]">
                                CSS Engine
                            </span>
                            <span class="font-mono text-[11px] text-[#787774] dark:text-[#8E8D8A]">Vite v8</span>
                        </div>
                        <h3 class="font-serif text-lg text-[#111111] dark:text-[#EDEDEC] mb-2 font-normal">
                            Tailwind CSS v4.0
                        </h3>
                        <p class="text-xs text-[#787774] dark:text-[#9B9B97] leading-[1.6]">
                            Lightning compilation via Oxide engine. No legacy JavaScript configuration file; fully native CSS variables and cascade layers.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#EAEAEA] dark:border-[#262626] text-[11px] font-mono text-[#787774] dark:text-[#8E8D8A]">
                        Plugin: @tailwindcss/vite
                    </div>
                </div>

                <!-- Bento Cell 4: Testing & Quality -->
                <div class="md:col-span-4 border border-[#EAEAEA] dark:border-[#262626] rounded-[8px] bg-white dark:bg-[#161615] p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#FDEBEC] text-[#9F2F2D] dark:bg-[#311617] dark:text-[#E88C8A]">
                                Test Runner
                            </span>
                            <span class="font-mono text-[11px] text-[#787774] dark:text-[#8E8D8A]">Pest v5.1</span>
                        </div>
                        <h3 class="font-serif text-lg text-[#111111] dark:text-[#EDEDEC] mb-2 font-normal">
                            Rigorous test verification
                        </h3>
                        <p class="text-xs text-[#787774] dark:text-[#9B9B97] leading-[1.6]">
                            First-class feature and unit testing suite with expressive Pest assertions and automated Livewire assertions.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#EAEAEA] dark:border-[#262626] text-[11px] font-mono text-[#787774] dark:text-[#8E8D8A]">
                        Command: php artisan test
                    </div>
                </div>

                <!-- Bento Cell 5: Database & Persistence -->
                <div class="md:col-span-4 border border-[#EAEAEA] dark:border-[#262626] rounded-[8px] bg-white dark:bg-[#161615] p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#FBF3DB] text-[#956400] dark:bg-[#2F2917] dark:text-[#E0BE69]">
                                Persistence
                            </span>
                            <span class="font-mono text-[11px] text-[#787774] dark:text-[#8E8D8A]">Eloquent</span>
                        </div>
                        <h3 class="font-serif text-lg text-[#111111] dark:text-[#EDEDEC] mb-2 font-normal">
                            Declarative migrations
                        </h3>
                        <p class="text-xs text-[#787774] dark:text-[#9B9B97] leading-[1.6]">
                            Zero-configuration database management, model factories, and database seeders pre-configured for local development.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#EAEAEA] dark:border-[#262626] text-[11px] font-mono text-[#787774] dark:text-[#8E8D8A]">
                        Driver: {{ config('database.default', 'sqlite') }}
                    </div>
                </div>
            </section>

            <!-- Technical Specifications Accordion / Protocol Section -->
            <section class="mb-16">
                <div class="mb-6">
                    <span class="font-mono text-xs uppercase tracking-wider text-[#787774] dark:text-[#8E8D8A]">
                        Protocol References
                    </span>
                    <h2 class="font-serif text-2xl text-[#111111] dark:text-[#EDEDEC] mt-1 font-normal">
                        Core system architecture
                    </h2>
                </div>

                <div class="divide-y divide-[#EAEAEA] dark:divide-[#262626] border-t border-b border-[#EAEAEA] dark:border-[#262626]">
                    <div class="py-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <span class="font-mono text-xs text-[#787774] dark:text-[#8E8D8A]">01</span>
                            <div>
                                <h4 class="text-xs font-medium text-[#111111] dark:text-[#EDEDEC]">Livewire 4 Single-File Components</h4>
                                <p class="text-xs text-[#787774] dark:text-[#9B9B97]">Volt provides functional and class-based reactive frontends with unified lifecycle hooks.</p>
                            </div>
                        </div>
                        <span class="font-mono text-xs text-[#787774] dark:text-[#8E8D8A]">Installed</span>
                    </div>

                    <div class="py-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <span class="font-mono text-xs text-[#787774] dark:text-[#8E8D8A]">02</span>
                            <div>
                                <h4 class="text-xs font-medium text-[#111111] dark:text-[#EDEDEC]">Laravel Boost MCP Integration</h4>
                                <p class="text-xs text-[#787774] dark:text-[#9B9B97]">Direct AI pair-programming assistant integration with specialized tools and guidelines.</p>
                            </div>
                        </div>
                        <span class="font-mono text-xs text-[#787774] dark:text-[#8E8D8A]">Configured</span>
                    </div>

                    <div class="py-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <span class="font-mono text-xs text-[#787774] dark:text-[#8E8D8A]">03</span>
                            <div>
                                <h4 class="text-xs font-medium text-[#111111] dark:text-[#EDEDEC]">Editorial Minimalist Aesthetics</h4>
                                <p class="text-xs text-[#787774] dark:text-[#9B9B97]">Strict warm monochrome palette, micro-whitespace, no drop-shadows, bespoke typography.</p>
                            </div>
                        </div>
                        <span class="font-mono text-xs text-[#787774] dark:text-[#8E8D8A]">Active</span>
                    </div>
                </div>
            </section>
        </main>

        <!-- Document Footer -->
        <footer class="w-full border-t border-[#EAEAEA] dark:border-[#202020] bg-[#FBFBFA] dark:bg-[#0E0E0E] py-8 text-xs font-mono text-[#787774] dark:text-[#8E8D8A]">
            <div class="max-w-5xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span>{{ config('app.name', 'SIRA') }}</span>
                    <span>&middot;</span>
                    <span>Laravel v{{ app()->version() }}</span>
                    <span>&middot;</span>
                    <span>PHP v{{ PHP_VERSION }}</span>
                </div>
                <div class="flex items-center gap-6">
                    <a href="https://laravel.com" target="_blank" rel="noopener" class="hover:text-[#111111] dark:hover:text-[#EDEDED] transition-colors">Laravel</a>
                    <a href="https://livewire.laravel.com" target="_blank" rel="noopener" class="hover:text-[#111111] dark:hover:text-[#EDEDED] transition-colors">Livewire</a>
                    <a href="https://github.com/livewire/volt" target="_blank" rel="noopener" class="hover:text-[#111111] dark:hover:text-[#EDEDED] transition-colors">Volt</a>
                    <a href="https://tailwindcss.com" target="_blank" rel="noopener" class="hover:text-[#111111] dark:hover:text-[#EDEDED] transition-colors">Tailwind CSS</a>
                </div>
            </div>
        </footer>

        @livewireScripts

        <!-- Theme Toggle Controller -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const toggleBtn = document.getElementById('theme-toggle');
                const lightIcon = document.getElementById('theme-toggle-light-icon');
                const darkIcon = document.getElementById('theme-toggle-dark-icon');
                const themeText = document.getElementById('theme-toggle-text');

                function syncThemeUI() {
                    const isDark = document.documentElement.classList.contains('dark');
                    if (isDark) {
                        lightIcon.classList.remove('hidden');
                        darkIcon.classList.add('hidden');
                        themeText.textContent = 'Light';
                    } else {
                        lightIcon.classList.add('hidden');
                        darkIcon.classList.remove('hidden');
                        themeText.textContent = 'Dark';
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
    </body>
</html>
