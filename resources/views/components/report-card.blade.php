@props(['report'])

<!-- Component: Report Card (Minimalist Document Style) -->
<div class="border border-[#EAEAEA] dark:border-[#222222] bg-white dark:bg-[#141414] rounded-[8px] overflow-hidden flex flex-col justify-between hover:border-[#CCCCCC] dark:hover:border-[#333333] transition duration-150 group">
    <div>
        <!-- Foto Thumbnail (Base64) -->
        <div class="relative h-44 w-full bg-[#F7F6F3] dark:bg-[#1C1C1C] border-b border-[#EAEAEA] dark:border-[#222222] overflow-hidden">
            <a href="{{ route('reports.show', $report) }}" class="block w-full h-full">
                <img src="{{ $report->image_base64 }}" alt="{{ $report->title }}" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition duration-300">
            </a>

            <!-- Top Badges Overlay (Tier, Status, Pending Duration) -->
            <div class="absolute top-3 inset-x-3 flex items-start justify-between gap-2 pointer-events-none z-10">
                <!-- Rank Tier Pill Tag (Muted Pastels) -->
                <div class="pointer-events-auto">
                    @if ($report->rank_tier === 'critical')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#FDEBEC] text-[#9F2F2D] border border-[#9F2F2D]/20">
                            Kritis
                        </span>
                    @elseif ($report->rank_tier === 'urgent')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#FBF3DB] text-[#956400] border border-[#956400]/20">
                            Mendesak
                        </span>
                    @elseif ($report->rank_tier === 'trending')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#EDF3EC] text-[#346538] border border-[#346538]/20">
                            Trending
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#F7F6F3] text-[#787774] border border-[#EAEAEA]">
                            Biasa
                        </span>
                    @endif
                </div>

                <!-- Status & Pending Duration Tag -->
                <div class="flex items-center flex-wrap justify-end gap-1.5 pointer-events-auto">
                    @if ($report->status === 'active')
                        <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-[4px] text-[10px] font-mono font-medium tracking-wide bg-[#FBF3DB]/95 dark:bg-[#2C2411]/95 text-[#956400] dark:text-[#E9C369] border border-[#956400]/30 shadow-xs backdrop-blur-xs" title="Laporan belum diproses selama {{ $report->pending_duration }} sejak awal diunggah">
                            <flux:icon name="clock" class="w-3 h-3 text-[#956400] dark:text-[#E9C369] shrink-0" />
                            <span>{{ $report->pending_duration }} belum diproses</span>
                        </span>
                    @endif

                    <span class="inline-flex items-center px-2 py-0.5 rounded-[4px] text-[10px] font-mono tracking-wider uppercase bg-white/90 dark:bg-[#111111]/90 text-[#111111] dark:text-[#EDEDEC] border border-[#EAEAEA] dark:border-[#282828]">
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
        </div>

        <!-- Body Detail -->
        <div class="p-5 space-y-2.5">
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

            <p class="text-xs text-[#787774] dark:text-[#9B9B97] line-clamp-2 leading-relaxed font-sans">
                {{ $report->description }}
            </p>
        </div>
    </div>

    <!-- Card Footer (Meta & Score) -->
    <div class="px-5 py-3 border-t border-[#EAEAEA] dark:border-[#222222] bg-[#FBFBFA]/50 dark:bg-[#111111]/50 flex items-center justify-between text-xs font-mono">
        <div class="text-[11px] text-[#787774]">
            <span>@<span>{{ $report->user->username ?? 'anon' }}</span></span>
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
