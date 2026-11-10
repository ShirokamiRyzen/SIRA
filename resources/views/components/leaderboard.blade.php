@props(['reports' => []])

<!-- Component: Leaderboard Widget (Editorial Minimalist) -->
<div class="border border-sand-100 dark:border-sand-850 bg-sand-25 dark:bg-sand-950 rounded-xl p-3.5 sm:p-5 lg:p-6 space-y-3 sm:space-y-3.5 shadow-xs">
    <div class="border-b border-sand-100 dark:border-sand-850 pb-3 flex items-center justify-between">
        <div>
            <h3 class="font-sans text-base font-bold text-sand-975 dark:text-sand-100 tracking-tight">
                Prioritas Utama
            </h3>
            <p class="text-[11px] font-mono text-sand-600 mt-0.5">Laporan Paling Banyak Didukung</p>
        </div>
        <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-mono bg-danger-tint text-danger">
            Live
        </span>
    </div>

    @if (empty($reports) || $reports->isEmpty())
        <p class="text-xs text-sand-600 py-3 text-center font-mono">Belum ada laporan prioritas.</p>
    @else
        <div class="space-y-2.5 font-sans">
            @foreach ($reports as $index => $crit)
                <a href="{{ route('reports.show', $crit) }}" class="flex items-start space-x-3 p-2.5 rounded-md hover:bg-white dark:hover:bg-sand-900 border border-transparent hover:border-sand-100 dark:hover:border-sand-800 transition duration-150 group">
                    <span class="font-mono text-xs font-bold text-sand-600 w-5 pt-0.5 shrink-0">
                        {{ sprintf('%02d', $index + 1) }}.
                    </span>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-semibold text-sand-975 dark:text-sand-100 truncate group-hover:underline underline-offset-2">
                            {{ $crit->title }}
                        </h4>
                        <div class="flex items-center space-x-2 mt-1 text-[10px] font-mono text-sand-600 truncate">
                            <span>{{ $crit->district ?? $crit->city ?? 'Area Publik' }}</span>
                            <span>&bull;</span>
                            <span class="font-bold text-danger">{{ $crit->vote_score }} suara</span>
                            @if ($crit->status === 'active')
                                <span>&bull;</span>
                                <span class="text-warning dark:text-warning-soft inline-flex items-center space-x-0.5 shrink-0" title="Belum diproses selama {{ $crit->pending_duration }}">
                                    <flux:icon name="clock" class="w-2.5 h-2.5 shrink-0" />
                                    <span>{{ $crit->pending_duration }}</span>
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <div class="pt-2 border-t border-sand-100 dark:border-sand-900">
        <a href="{{ route('heatmap.index') }}" class="text-[11px] font-mono text-sand-975 dark:text-sand-100 hover:underline underline-offset-4 flex items-center justify-between">
            <span>Visualisasi Heatmap</span>
            <span>&rarr;</span>
        </a>
    </div>
</div>
