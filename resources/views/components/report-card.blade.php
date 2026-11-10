@props(['report'])

<!-- Component: Report Card (Minimalist Document Style) -->
<div class="border border-sand-100 dark:border-sand-900 bg-white dark:bg-sand-975 rounded-xl overflow-hidden flex flex-col justify-between hover:border-sand-300 dark:hover:border-sand-800 transition duration-150 group">
    <div>
        <!-- Foto Thumbnail (Base64) -->
        <div class="relative h-44 w-full bg-sand-50 dark:bg-sand-925 border-b border-sand-100 dark:border-sand-900 overflow-hidden">
            <a href="{{ route('reports.show', $report) }}" class="block w-full h-full">
                <img src="{{ $report->image_base64 }}" alt="{{ $report->title }}" class="w-full h-full object-cover transition duration-300">
            </a>

            <!-- Top Badges Overlay (Tier, Category / Multi-Masalah, Status) -->
            <div class="absolute top-2 inset-x-2 sm:top-2.5 sm:inset-x-2.5 flex items-center justify-between gap-1.5 pointer-events-none z-10">
                <!-- Rank Tier & Category Pill Tag -->
                <div class="pointer-events-auto flex items-center gap-1 sm:gap-1.5 flex-nowrap min-w-0">
                    @if ($report->rank_tier === 'critical')
                        <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-mono tracking-wider uppercase bg-danger-tint text-danger border border-danger/20 shrink-0">
                            Kritis
                        </span>
                    @elseif ($report->rank_tier === 'urgent')
                        <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-mono tracking-wider uppercase bg-warning-tint text-warning border border-warning/20 shrink-0">
                            Mendesak
                        </span>
                    @elseif ($report->rank_tier === 'trending')
                        <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-mono tracking-wider uppercase bg-success-tint text-success border border-success/20 shrink-0">
                            Trending
                        </span>
                    @else
                        <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-mono tracking-wider uppercase bg-sand-50 text-sand-600 border border-sand-100 shrink-0">
                            Biasa
                        </span>
                    @endif

                    @if ($report->is_multi_issue)
                        <span class="inline-flex items-center gap-1 px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-semibold bg-violet-100 text-violet-900 dark:bg-violet-950/70 dark:text-violet-200 border border-violet-300/80 dark:border-violet-800/80 shadow-xs shrink-0" title="Terdeteksi {{ $report->total_location_issues }} masalah berbeda di titik lokasi yang sama">
                            <flux:icon name="squares-2x2" class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-violet-700 dark:text-violet-300 shrink-0" />
                            <span>Multi Masalah</span>
                            <span class="inline-flex items-center justify-center min-w-3.5 h-3.5 px-1 rounded-full text-[9px] font-mono font-bold bg-violet-200 text-violet-900 dark:bg-violet-900 dark:text-violet-100">
                                {{ $report->total_location_issues }}
                            </span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-medium {{ $report->category_meta['badge_class'] }} shrink-0">
                            <flux:icon name="{{ $report->category_icon }}" class="w-2.5 h-2.5 sm:w-3 sm:h-3 shrink-0" />
                            <span>{{ $report->category_label }}</span>
                        </span>
                    @endif
                </div>

                <!-- Status Tag -->
                <div class="pointer-events-auto shrink-0">
                    <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-md text-[9px] sm:text-[10px] font-mono tracking-wider uppercase bg-white/95 dark:bg-sand-975/95 text-sand-975 dark:text-sand-100 border border-sand-100 dark:border-sand-850 shadow-xs backdrop-blur-xs">
                        @if ($report->status === 'resolved')
                            Selesai
                        @elseif ($report->status === 'in_progress')
                            Diproses
                        @else
                            Aktif
                        @endif
                    </span>
                </div>
            </div>

            <!-- Bottom-Left Badge Overlay: Pending Duration (Clean Separation, No Collision) -->
            @if ($report->status === 'active')
                <div class="absolute bottom-2 left-2 sm:bottom-2.5 sm:left-2.5 pointer-events-none z-10 max-w-[calc(100%-1rem)]">
                    <span class="pointer-events-auto inline-flex items-center space-x-1 px-1.5 sm:px-2 py-0.5 rounded-md text-[9px] sm:text-[10px] font-mono font-medium tracking-wide bg-warning-tint/95 dark:bg-warning-deep/95 text-warning dark:text-warning-soft border border-warning/30 shadow-xs backdrop-blur-xs" title="Laporan belum diproses selama {{ $report->pending_duration }} sejak awal diunggah">
                        <flux:icon name="clock" class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-warning dark:text-warning-soft shrink-0" />
                        <span>{{ $report->pending_duration }} belum diproses</span>
                    </span>
                </div>
            @endif
        </div>

        <!-- Body Detail -->
        <div class="p-4 sm:p-5 space-y-2 sm:space-y-2.5">
            <div class="flex items-center space-x-1.5 text-[11px] font-mono text-sand-600">
                <flux:icon name="map-pin" class="w-3.5 h-3.5 text-sand-600 shrink-0" />
                <span class="line-clamp-1 break-words">
                    {{ $report->district ?? $report->city ?? 'Lokasi Terdaftar' }}
                    @if ($report->city && $report->district && strcasecmp($report->city, $report->district) !== 0) &bull; {{ $report->city }} @endif
                </span>
            </div>

            <h3 class="font-sans font-semibold text-sm text-sand-975 dark:text-sand-100 leading-snug line-clamp-2 group-hover:underline underline-offset-2">
                <a href="{{ route('reports.show', $report) }}">
                    {{ $report->title }}
                </a>
            </h3>

            @php
                $cardDesc = preg_replace_callback('/(^|[^a-zA-Z0-9_])@([a-zA-Z0-9_]+)/', function($m) {
                    return $m[1] . '<span class="font-bold text-blue-600 dark:text-blue-400">@' . $m[2] . '</span>';
                }, e($report->description));
            @endphp
            <p class="text-xs text-sand-600 dark:text-sand-500 line-clamp-2 leading-relaxed font-sans">
                {!! $cardDesc !!}
            </p>
        </div>
    </div>

    <!-- Card Footer (Meta & Score) -->
    <div class="px-4 sm:px-5 py-2.5 sm:py-3 border-t border-sand-100 dark:border-sand-900 bg-sand-25/50 dark:bg-sand-975/50 flex items-center justify-between text-xs font-mono">
        <div class="text-[11px] text-sand-600 flex items-center space-x-1">
            <span>@<span>{{ $report->user->username ?? 'anon' }}</span></span>
            @if ($report->user)
                <x-verified-badge :user="$report->user" size="xs" />
            @endif
        </div>

        <div class="flex items-center space-x-3 text-[11px]">
            <div class="flex items-center space-x-1 font-bold text-sand-975 dark:text-sand-100">
                <flux:icon name="hand-thumb-up" class="w-3 h-3 text-warning shrink-0" />
                <span>{{ $report->vote_score }}</span>
            </div>
            <div class="flex items-center space-x-1 text-sand-600">
                <flux:icon name="chat-bubble-left" class="w-3 h-3 text-sand-600 shrink-0" />
                <span>{{ $report->comments_count }}</span>
            </div>
        </div>
    </div>
</div>
