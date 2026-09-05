@props(['reports' => []])

<!-- Component: Leaderboard Widget (Editorial Minimalist) -->
<div class="border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] rounded-[8px] p-6 space-y-4">
    <div class="border-b border-[#EAEAEA] dark:border-[#222222] pb-3 flex items-center justify-between">
        <div>
            <h3 class="font-serif text-base font-medium text-[#111111] dark:text-[#EDEDEC] tracking-tight">
                Prioritas Utama
            </h3>
            <p class="text-[11px] font-mono text-[#787774] mt-0.5">Top Laporan Tervote</p>
        </div>
        <span class="inline-flex items-center px-1.5 py-0.5 rounded-[4px] text-[10px] font-mono bg-[#FDEBEC] text-[#9F2F2D]">
            Live
        </span>
    </div>

    @if (empty($reports) || $reports->isEmpty())
        <p class="text-xs text-[#787774] py-3 text-center font-mono">Belum ada laporan prioritas.</p>
    @else
        <div class="space-y-3 font-sans">
            @foreach ($reports as $index => $crit)
                <a href="{{ route('reports.show', $crit) }}" class="flex items-start space-x-3 p-2 rounded-[6px] hover:bg-[#F7F6F3] dark:hover:bg-[#1C1C1C] transition duration-150 group">
                    <span class="font-mono text-xs font-bold text-[#787774] w-5 pt-0.5 shrink-0">
                        {{ sprintf('%02d', $index + 1) }}.
                    </span>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-semibold text-[#111111] dark:text-[#EDEDEC] truncate group-hover:underline underline-offset-2">
                            {{ $crit->title }}
                        </h4>
                        <div class="flex items-center space-x-2 mt-1 text-[10px] font-mono text-[#787774]">
                            <span>{{ $crit->district ?? $crit->city ?? 'Area Publik' }}</span>
                            <span>&bull;</span>
                            <span class="font-bold text-[#9F2F2D]">{{ $crit->vote_score }} votes</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <div class="pt-2 border-t border-[#EAEAEA] dark:border-[#222222]">
        <a href="{{ route('heatmap.index') }}" class="text-[11px] font-mono text-[#111111] dark:text-[#EDEDEC] hover:underline underline-offset-4 flex items-center justify-between">
            <span>Visualisasi Heatmap</span>
            <span>&rarr;</span>
        </a>
    </div>
</div>
