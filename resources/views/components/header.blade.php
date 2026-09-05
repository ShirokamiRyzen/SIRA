<!-- Component: Header Navigation (Minimalist Utilitarian) -->
<header class="bg-[#FBFBFA]/85 dark:bg-[#0E0E0E]/85 backdrop-blur-md border-b border-[#EAEAEA] dark:border-[#202020] sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Brand Logo -->
        <div class="flex items-center space-x-6">
            <a href="{{ url('/') }}" class="flex items-center space-x-2.5 group">
                <span class="w-7 h-7 rounded-[4px] bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111] flex items-center justify-center font-mono text-xs font-semibold">
                    S
                </span>
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-semibold tracking-tight text-[#111111] dark:text-[#EDEDEC]">SIRA</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#EDF3EC] text-[#346538] dark:bg-[#1C281E] dark:text-[#82C78A]">
                        Pengawasan Warga
                    </span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center space-x-1 font-mono text-xs">
                <a href="{{ route('reports.index') }}" class="px-3 py-1.5 rounded-[6px] transition-colors {{ request()->routeIs('reports.index') ? 'text-[#111111] dark:text-[#EDEDEC] bg-[#EAEAEA]/80 dark:bg-[#222222] font-medium' : 'text-[#787774] dark:text-[#9B9B97] hover:text-[#111111] dark:hover:text-[#EDEDEC] hover:bg-[#EAEAEA]/40 dark:hover:bg-[#1E1E1E]' }}">
                    Dasbor Laporan
                </a>
                <a href="{{ route('heatmap.index') }}" class="px-3 py-1.5 rounded-[6px] transition-colors flex items-center space-x-1.5 {{ request()->routeIs('heatmap.index') ? 'text-[#111111] dark:text-[#EDEDEC] bg-[#EAEAEA]/80 dark:bg-[#222222] font-medium' : 'text-[#787774] dark:text-[#9B9B97] hover:text-[#111111] dark:hover:text-[#EDEDEC] hover:bg-[#EAEAEA]/40 dark:hover:bg-[#1E1E1E]' }}">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#9F2F2D]"></span>
                    <span>Peta Sebaran</span>
                </a>
                <a href="{{ url('/#cara-kerja') }}" class="px-3 py-1.5 rounded-[6px] transition-colors text-[#787774] dark:text-[#9B9B97] hover:text-[#111111] dark:hover:text-[#EDEDEC] hover:bg-[#EAEAEA]/40 dark:hover:bg-[#1E1E1E] hidden lg:inline-block">
                    Cara Kerja
                </a>
                <a href="{{ url('/#fitur') }}" class="px-3 py-1.5 rounded-[6px] transition-colors text-[#787774] dark:text-[#9B9B97] hover:text-[#111111] dark:hover:text-[#EDEDEC] hover:bg-[#EAEAEA]/40 dark:hover:bg-[#1E1E1E] hidden lg:inline-block">
                    Fitur Utama
                </a>
            </nav>
        </div>

        <!-- Action & Auth Navigation -->
        <div class="flex items-center space-x-2.5 sm:space-x-3 font-mono text-xs">
            <!-- Tombol Pengalih Tema (Light / Dark) -->
            <button
                id="theme-toggle"
                type="button"
                aria-label="Ganti tema warna"
                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#161615] text-[#111111] dark:text-[#EDEDEC] hover:bg-[#F7F6F3] dark:hover:bg-[#1F1F1E] transition-all active:scale-[0.98] font-mono text-[11px] cursor-pointer"
            >
                <svg id="theme-toggle-light-icon" class="w-3.5 h-3.5 hidden" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75">
                    <circle cx="8" cy="8" r="3"/>
                    <path d="M8 1.5v1.5M8 13v1.5M1.5 8h1.5M13 8h1.5M3.4 3.4l1.1 1.1M11.5 11.5l1.1 1.1M3.4 12.6l1.1-1.1M11.5 4.5l1.1-1.1" stroke-linecap="square"/>
                </svg>
                <svg id="theme-toggle-dark-icon" class="w-3.5 h-3.5 hidden" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75">
                    <path d="M13.5 9.8A6 6 0 1 1 6.2 2.5a4.8 4.8 0 0 0 7.3 7.3z" stroke-linecap="square"/>
                </svg>
                <span id="theme-toggle-text">Tema</span>
            </button>

            <a href="{{ route('reports.create') }}" class="inline-flex items-center space-x-1 bg-[#111111] hover:bg-[#2A2A2A] active:scale-[0.98] text-white dark:bg-[#EDEDEC] dark:text-[#111111] dark:hover:bg-white text-xs font-medium px-3.5 py-1.5 rounded-[6px] transition duration-150 shrink-0">
                <span>+ Buat Laporan</span>
            </a>

            @auth
                <div class="flex items-center space-x-2 sm:space-x-3 pl-2 sm:pl-3 border-l border-[#EAEAEA] dark:border-[#282828]">
                    <span class="text-[#787774] dark:text-[#9B9B97] text-[11px] hidden sm:inline">@<span>{{ Auth::user()->username }}</span></span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="Keluar" class="text-[#787774] hover:text-[#9F2F2D] dark:hover:text-[#E88C8A] transition-colors">
                            [Keluar]
                        </button>
                    </form>
                </div>
            @else
                <div class="flex items-center space-x-1.5 sm:space-x-2 pl-2 sm:pl-3 border-l border-[#EAEAEA] dark:border-[#282828]">
                    <a href="{{ route('login') }}" class="text-[#787774] hover:text-[#111111] dark:text-[#9B9B97] dark:hover:text-[#EDEDEC] px-2 py-1.5 transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="text-[#111111] dark:text-[#111111] bg-[#EAEAEA] hover:bg-[#E0E0E0] dark:bg-[#EDEDEC] dark:hover:bg-white px-2.5 py-1.5 rounded-[6px] font-medium transition-colors">
                        Daftar
                    </a>
                </div>
            @endauth
        </div>
    </div>
</header>
