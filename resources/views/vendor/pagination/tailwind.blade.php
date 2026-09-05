@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 font-mono text-xs select-none">
        <!-- Keterangan Jumlah Laporan -->
        <div class="text-[#787774] dark:text-[#888888] text-center sm:text-left order-2 sm:order-1">
            Menampilkan
            @if ($paginator->firstItem())
                <span class="font-semibold text-[#111111] dark:text-[#EDEDEC]">{{ number_format($paginator->firstItem(), 0, ',', '.') }}</span>
                &ndash;
                <span class="font-semibold text-[#111111] dark:text-[#EDEDEC]">{{ number_format($paginator->lastItem(), 0, ',', '.') }}</span>
            @else
                <span class="font-semibold text-[#111111] dark:text-[#EDEDEC]">{{ number_format($paginator->count(), 0, ',', '.') }}</span>
            @endif
            dari
            <span class="font-semibold text-[#111111] dark:text-[#EDEDEC]">{{ number_format($paginator->total(), 0, ',', '.') }}</span>
            laporan
        </div>

        <!-- Tombol Kontrol Halaman -->
        <div class="flex items-center gap-1.5 order-1 sm:order-2">
            {{-- Tombol Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center px-2.5 h-8 rounded-[6px] border border-[#EAEAEA] dark:border-[#252525] bg-[#F7F6F3]/50 dark:bg-[#141414] text-[#B0B0B0] dark:text-[#555555] cursor-not-allowed select-none text-[11px]">
                    <flux:icon name="chevron-left" class="w-3.5 h-3.5 sm:mr-1 shrink-0" />
                    <span class="hidden sm:inline">Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center px-2.5 h-8 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#161615] text-[#111111] dark:text-[#EDEDEC] hover:bg-[#F7F6F3] dark:hover:bg-[#1F1F1E] active:scale-95 transition text-[11px] font-medium shadow-2xs">
                    <flux:icon name="chevron-left" class="w-3.5 h-3.5 sm:mr-1 shrink-0" />
                    <span class="hidden sm:inline">Sebelumnya</span>
                </a>
            @endif

            {{-- Nomor Halaman (Desktop) --}}
            <div class="hidden sm:flex items-center gap-1">
                @foreach ($elements as $element)
                    {{-- Separator Titik Tiga --}}
                    @if (is_string($element))
                        <span class="inline-flex items-center justify-center w-7 h-8 text-[#999999] dark:text-[#666666] select-none text-xs">
                            &hellip;
                        </span>
                    @endif

                    {{-- Daftar Link Halaman --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="inline-flex items-center justify-center min-w-[32px] px-2 h-8 rounded-[6px] bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111] font-bold text-xs shadow-xs select-none">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="inline-flex items-center justify-center min-w-[32px] px-2 h-8 rounded-[6px] text-[#787774] dark:text-[#9B9B97] hover:text-[#111111] dark:hover:text-[#EDEDEC] hover:bg-[#EAEAEA]/60 dark:hover:bg-[#202020] transition text-xs">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Indikator Ringkas untuk Mobile --}}
            <div class="sm:hidden px-2 text-[11px] text-[#787774] dark:text-[#9B9B97]">
                <span class="font-bold text-[#111111] dark:text-[#EDEDEC]">{{ $paginator->currentPage() }}</span>
                /
                <span>{{ $paginator->lastPage() }}</span>
            </div>

            {{-- Tombol Berikutnya --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center px-2.5 h-8 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#161615] text-[#111111] dark:text-[#EDEDEC] hover:bg-[#F7F6F3] dark:hover:bg-[#1F1F1E] active:scale-95 transition text-[11px] font-medium shadow-2xs">
                    <span class="hidden sm:inline mr-1">Berikutnya</span>
                    <flux:icon name="chevron-right" class="w-3.5 h-3.5 shrink-0" />
                </a>
            @else
                <span class="inline-flex items-center justify-center px-2.5 h-8 rounded-[6px] border border-[#EAEAEA] dark:border-[#252525] bg-[#F7F6F3]/50 dark:bg-[#141414] text-[#B0B0B0] dark:text-[#555555] cursor-not-allowed select-none text-[11px]">
                    <span class="hidden sm:inline mr-1">Berikutnya</span>
                    <flux:icon name="chevron-right" class="w-3.5 h-3.5 shrink-0" />
                </span>
            @endif
        </div>
    </nav>
@endif
