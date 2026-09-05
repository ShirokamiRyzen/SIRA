<!-- Component: Header Navigation (Minimalist Utilitarian) -->
<header class="bg-[#FBFBFA]/80 dark:bg-[#0E0E0E]/80 backdrop-blur-md border-b border-[#EAEAEA] dark:border-[#202020] sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Brand Logo -->
        <div class="flex items-center space-x-6">
            <a href="{{ route('reports.index') }}" class="flex items-center space-x-2.5 group">
                <span class="w-7 h-7 rounded-[4px] bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111] flex items-center justify-center font-mono text-xs font-semibold">
                    S
                </span>
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-semibold tracking-tight text-[#111111] dark:text-[#EDEDEC]">SIRA</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#EDF3EC] text-[#346538] dark:bg-[#1C281E] dark:text-[#82C78A]">
                        Public Ledger
                    </span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center space-x-1 font-mono text-xs">
                <a href="{{ route('reports.index') }}" class="px-3 py-1.5 rounded-[6px] transition-colors {{ request()->routeIs('reports.index') ? 'text-[#111111] bg-[#EAEAEA]/80 font-medium' : 'text-[#787774] hover:text-[#111111] hover:bg-[#EAEAEA]/40' }}">
                    Dashboard Laporan
                </a>
                <a href="{{ route('heatmap.index') }}" class="px-3 py-1.5 rounded-[6px] transition-colors flex items-center space-x-1.5 {{ request()->routeIs('heatmap.index') ? 'text-[#111111] bg-[#EAEAEA]/80 font-medium' : 'text-[#787774] hover:text-[#111111] hover:bg-[#EAEAEA]/40' }}">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#9F2F2D]"></span>
                    <span>Peta Heatmap</span>
                </a>
            </nav>
        </div>

        <!-- Action & Auth Navigation -->
        <div class="flex items-center space-x-3 font-mono text-xs">
            <a href="{{ route('reports.create') }}" class="inline-flex items-center space-x-1 bg-[#111111] hover:bg-[#2A2A2A] active:scale-[0.98] text-white text-xs font-medium px-3.5 py-2 rounded-[6px] transition duration-150">
                <span>+ Buat Laporan</span>
            </a>

            @auth
                <div class="flex items-center space-x-3 pl-3 border-l border-[#EAEAEA]">
                    <span class="text-[#787774]">@<span>{{ Auth::user()->username }}</span></span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="Logout" class="text-[#787774] hover:text-[#9F2F2D] transition-colors">
                            [Keluar]
                        </button>
                    </form>
                </div>
            @else
                <div class="flex items-center space-x-2 pl-3 border-l border-[#EAEAEA]">
                    <a href="{{ route('login') }}" class="text-[#787774] hover:text-[#111111] px-2.5 py-1.5 transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="text-[#111111] bg-[#EAEAEA] hover:bg-[#E0E0E0] px-3 py-1.5 rounded-[6px] font-medium transition-colors">
                        Daftar
                    </a>
                </div>
            @endauth
        </div>
    </div>
</header>
