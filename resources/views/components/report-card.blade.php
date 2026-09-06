@props(['report'])

<!-- Component: Report Card (Minimalist Document Style) -->
<div class="border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] rounded-[8px] overflow-hidden flex flex-col justify-between hover:border-[#CCCCCC] dark:hover:border-[#333333] transition duration-150 group">
    <div>
        <!-- Foto Thumbnail (Base64) -->
        <div class="relative h-44 w-full bg-[#F7F6F3] dark:bg-[#1C1C1C] border-b border-[#EAEAEA] dark:border-[#222222] overflow-hidden">
            <a href="{{ route('reports.show', $report) }}" class="block w-full h-full">
                <img src="{{ $report->image_base64 }}" alt="{{ $report->title }}" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition duration-300">
            </a>

            <!-- Top Badges Overlay (Tier, Category / Multi-Masalah, Status) -->
            <div class="absolute top-2 inset-x-2 sm:top-2.5 sm:inset-x-2.5 flex items-center justify-between gap-1.5 pointer-events-none z-10">
                <!-- Rank Tier & Category Pill Tag -->
                <div class="pointer-events-auto flex items-center gap-1 sm:gap-1.5 flex-nowrap min-w-0">
                    @if ($report->rank_tier === 'critical')
                        <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-mono tracking-wider uppercase bg-[#FDEBEC] text-[#9F2F2D] border border-[#9F2F2D]/20 shrink-0">
                            Kritis
                        </span>
                    @elseif ($report->rank_tier === 'urgent')
                        <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-mono tracking-wider uppercase bg-[#FBF3DB] text-[#956400] border border-[#956400]/20 shrink-0">
                            Mendesak
                        </span>
                    @elseif ($report->rank_tier === 'trending')
                        <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-mono tracking-wider uppercase bg-[#EDF3EC] text-[#346538] border border-[#346538]/20 shrink-0">
                            Trending
                        </span>
                    @else
                        <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-mono tracking-wider uppercase bg-[#F7F6F3] text-[#787774] border border-[#EAEAEA] shrink-0">
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
                        <span class="inline-flex items-center gap-1 px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-medium {{ $report->category_meta['badge_class'] }} truncate max-w-[110px] sm:max-w-none">
                            <flux:icon name="{{ $report->category_icon }}" class="w-2.5 h-2.5 sm:w-3 sm:h-3 shrink-0" />
                            <span class="truncate">{{ $report->category_label }}</span>
                        </span>
                    @endif
                </div>

                <!-- Status Tag -->
                <div class="pointer-events-auto shrink-0">
                    <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-[4px] text-[9px] sm:text-[10px] font-mono tracking-wider uppercase bg-white/95 dark:bg-[#111111]/95 text-[#111111] dark:text-[#EDEDEC] border border-[#EAEAEA] dark:border-[#282828] shadow-xs backdrop-blur-xs">
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
                    <span class="pointer-events-auto inline-flex items-center space-x-1 px-1.5 sm:px-2 py-0.5 rounded-[4px] text-[9px] sm:text-[10px] font-mono font-medium tracking-wide bg-[#FBF3DB]/95 dark:bg-[#2C2411]/95 text-[#956400] dark:text-[#E9C369] border border-[#956400]/30 shadow-xs backdrop-blur-xs truncate" title="Laporan belum diproses selama {{ $report->pending_duration }} sejak awal diunggah">
                        <flux:icon name="clock" class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-[#956400] dark:text-[#E9C369] shrink-0" />
                        <span class="truncate">{{ $report->pending_duration }} belum diproses</span>
                    </span>
                </div>
            @endif
        </div>

        <!-- Body Detail -->
        <div class="p-4 sm:p-5 space-y-2 sm:space-y-2.5">
            <div class="flex items-center space-x-1.5 text-[11px] font-mono text-[#787774] truncate">
                <flux:icon name="map-pin" class="w-3.5 h-3.5 text-[#787774] shrink-0" />
                <span class="truncate">
                    {{ $report->district ?? $report->city ?? 'Lokasi Terdaftar' }}
                    @if ($report->city && $report->district && strcasecmp($report->city, $report->district) !== 0) &bull; {{ $report->city }} @endif
                </span>
            </div>

            <h3 class="font-sans font-semibold text-sm text-[#111111] dark:text-[#EDEDEC] leading-snug line-clamp-2 group-hover:underline underline-offset-2">
                <a href="{{ route('reports.show', $report) }}">
                    {{ $report->title }}
                </a>
            </h3>

            @php
                $cardDesc = preg_replace_callback('/(^|[^a-zA-Z0-9_])@([a-zA-Z0-9_]+)/', function($m) {
                    return $m[1] . '<span class="font-semibold text-emerald-600 dark:text-emerald-400 font-mono">@' . $m[2] . '</span>';
                }, e($report->description));
            @endphp
            <p class="text-xs text-[#787774] dark:text-[#9B9B97] line-clamp-2 leading-relaxed font-sans">
                {!! $cardDesc !!}
            </p>
        </div>
    </div>

    <!-- Card Footer (Meta & Score) -->
    <div class="px-4 sm:px-5 py-2.5 sm:py-3 border-t border-[#EAEAEA] dark:border-[#222222] bg-[#FBFBFA]/50 dark:bg-[#111111]/50 flex items-center justify-between text-xs font-mono">
        <div class="text-[11px] text-[#787774] flex items-center space-x-1">
            <span>@<span>{{ $report->user->username ?? 'anon' }}</span></span>
            @if ($report->user)
                <x-verified-badge :user="$report->user" size="xs" />
            @endif
        </div>

        <div class="flex items-center space-x-3 text-[11px]">
            <div class="flex items-center space-x-1 font-bold text-[#111111] dark:text-[#EDEDEC]">
                <flux:icon name="hand-thumb-up" class="w-3 h-3 text-[#956400] shrink-0" />
                <span>{{ $report->vote_score }}</span>
            </div>
            <div class="flex items-center space-x-1 text-[#787774]">
                <flux:icon name="chat-bubble-left" class="w-3 h-3 text-[#787774] shrink-0" />
                <span>{{ $report->comments_count }}</span>
            </div>
        </div>
    </div>
</div>
