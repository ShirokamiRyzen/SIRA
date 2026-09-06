@extends('layouts.app')

@section('title', $report->title . ' - SIRA')

@section('og_title', '[' . strtoupper($report->status_label) . '] ' . $report->title . ' — SIRA')
@section('og_description', \Illuminate\Support\Str::limit($report->og_meta_description, 295))
@section('og_type', 'article')
@section('og_image', route('reports.ogImage', $report))
@section('og_url', route('reports.show', $report))

@push('styles')
    <style>
        /* Mention Live Highlighter in Textarea - Pixel-perfect Alignment & Clean Tag Sizing */
        .mention-highlighter-wrapper {
            position: relative;
        }

        .mention-backdrop,
        .mention-input {
            font-family: var(--font-sans), 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            letter-spacing: normal !important;
            word-spacing: normal !important;
            line-height: 1.625 !important; /* leading-relaxed (1.625) */
            white-space: pre-wrap !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            word-break: break-word !important;
            tab-size: 4 !important;
            -moz-tab-size: 4 !important;
            box-sizing: border-box !important;
            margin: 0 !important;
            border: 0 !important;
            border-radius: 0 !important;
        }

        /* Form Utama Komentar */
        .mention-highlighter-main .mention-backdrop,
        .mention-highlighter-main .mention-input {
            font-size: 0.875rem !important; /* 14px */
            padding: 12px 16px !important; /* px-4 py-3 */
        }

        /* Form Balasan Komentar (Reply) */
        .mention-highlighter-sm .mention-backdrop,
        .mention-highlighter-sm .mention-input {
            font-size: 0.75rem !important; /* 12px */
            padding: 8px 12px !important; /* px-3 py-2 */
        }

        .mention-backdrop {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            color: transparent !important;
            user-select: none;
            -webkit-user-select: none;
        }

        .mention-input {
            position: relative;
            z-index: 10;
            width: 100%;
            background-color: transparent !important;
            outline: none !important;
        }

        /* Styling Tag Highlight: pas, rapi, dan proporsional */
        .mention-tag {
            display: inline;
            color: transparent !important;
            font-weight: inherit !important;
            font-family: inherit !important;
            line-height: inherit !important;
            border-radius: 4px;
            padding: 1.5px 3px;
            margin: 0 -3px;
            box-decoration-break: clone;
            -webkit-box-decoration-break: clone;
        }

        /* Highlight untuk AI Bot @Sira */
        .mention-tag-sira {
            background-color: rgba(99, 102, 241, 0.18) !important;
            box-shadow: 0 0 0 1px rgba(99, 102, 241, 0.45) !important;
        }
        .dark .mention-tag-sira {
            background-color: rgba(99, 102, 241, 0.3) !important;
            box-shadow: 0 0 0 1px rgba(129, 140, 248, 0.6) !important;
        }

        /* Highlight untuk User Mention (@username) */
        .mention-tag-user {
            background-color: rgba(16, 185, 129, 0.18) !important;
            box-shadow: 0 0 0 1px rgba(16, 185, 129, 0.45) !important;
        }
        .dark .mention-tag-user {
            background-color: rgba(16, 185, 129, 0.3) !important;
            box-shadow: 0 0 0 1px rgba(52, 211, 153, 0.6) !important;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-5xl mx-auto space-y-8">
        <!-- Breadcrumb & Aksi Bagikan Kartu -->
        <div class="flex items-center justify-between">
            <a href="{{ route('reports.index') }}"
                class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-emerald-700 dark:hover:text-emerald-400 transition space-x-1">
                <span>&larr;</span>
                <span>Kembali ke Semua Laporan</span>
            </a>
            <button type="button" onclick="openOgCanvasModal()"
                class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-[#1A1A1A] border border-slate-200 dark:border-[#282828] text-slate-700 dark:text-[#EDEDEC] hover:bg-slate-50 dark:hover:bg-[#222222] transition shadow-xs cursor-pointer">
                <flux:icon name="share" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                <span>Bagikan Kartu (Canvas)</span>
            </button>
        </div>

        <!-- Main Card Header & Grid -->
        <div
            class="bg-white dark:bg-[#141414] rounded-3xl border border-slate-200 dark:border-[#222222] shadow-sm overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
                <!-- Kolom Kiri: Foto Bukti Base64 -->
                <div class="lg:col-span-7 bg-slate-950 flex items-center justify-center p-2 relative min-h-[350px]">
                    <img src="{{ $report->image_base64 }}" alt="{{ $report->title }}"
                        class="max-h-[500px] w-auto max-w-full object-contain rounded-xl">

                    <!-- Image Badges Overlay: Flex header so tier and pending duration never collide -->
                    <div class="absolute top-3 inset-x-3 sm:top-4 sm:inset-x-4 flex items-center justify-between gap-2 pointer-events-none z-10">
                        <div class="pointer-events-auto shrink-0" id="tierBadgeContainer">
                            @if ($report->rank_tier === 'critical')
                                <span
                                    class="inline-flex items-center space-x-1 sm:space-x-1.5 px-2.5 sm:px-3.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-black bg-rose-600 text-white shadow-lg">
                                    <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-white animate-ping"></span>
                                    <span>CRITICAL TIER</span>
                                </span>
                            @elseif ($report->rank_tier === 'urgent')
                                <span
                                    class="inline-flex items-center space-x-1 sm:space-x-1.5 px-2.5 sm:px-3.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold bg-amber-500 text-white shadow-md">
                                    <flux:icon name="exclamation-triangle" class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-white" />
                                    <span>URGENT TIER</span>
                                </span>
                            @elseif ($report->rank_tier === 'trending')
                                <span
                                    class="inline-flex items-center space-x-1 sm:space-x-1.5 px-2.5 sm:px-3.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold bg-teal-600 text-white shadow-md">
                                    <flux:icon name="arrow-trending-up" class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-white" />
                                    <span>TRENDING TIER</span>
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 sm:px-3.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-slate-800/80 backdrop-blur-md text-white">
                                    NORMAL TIER
                                </span>
                            @endif
                        </div>

                        <!-- Pending Duration Badge Overlay on Image -->
                        <div class="pointer-events-auto shrink-0 {{ $report->status === 'active' ? '' : 'hidden' }}"
                            id="imagePendingBadge">
                            <span
                                class="inline-flex items-center space-x-1 px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-mono font-semibold bg-[#FBF3DB]/95 dark:bg-[#2C2411]/95 text-[#956400] dark:text-[#E9C369] border border-[#956400]/30 shadow-md backdrop-blur-xs"
                                title="Laporan belum diproses selama {{ $report->pending_duration }} sejak awal diunggah">
                                <flux:icon name="clock" class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-[#956400] dark:text-[#E9C369] shrink-0" />
                                <span>{{ $report->pending_duration }} belum diproses</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Detail & Aksi Voting -->
                <div class="lg:col-span-5 p-4 sm:p-6 lg:p-8 flex flex-col justify-between space-y-5 sm:space-y-6">
                    <div class="space-y-4">
                        <!-- Status & Waktu & Aksi Khusus Pembuat Laporan -->
                        <div class="flex items-center justify-between text-xs flex-wrap gap-2 pb-1">
                            <div class="flex items-center space-x-1.5 sm:space-x-2 flex-wrap gap-y-1.5 sm:gap-y-2">
                                <span class="inline-flex items-center gap-1 sm:gap-1.5 px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-full font-bold text-[11px] sm:text-xs {{ $report->category_meta['badge_class'] }}">
                                    <flux:icon name="{{ $report->category_icon }}" class="w-3 h-3 sm:w-3.5 sm:h-3.5 shrink-0" />
                                    <span>{{ $report->category_label }}</span>
                                </span>

                                <span id="reportStatusBadge"
                                    class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full font-bold uppercase tracking-wider text-[10px] sm:text-xs transition duration-200 {{ $report->status === 'resolved' ? 'bg-emerald-600 text-white shadow-xs' : ($report->status === 'in_progress' ? 'bg-amber-100 text-amber-800 border border-amber-300 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800/60' : 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800/60') }}">
                                    Status: {{ str_replace('_', ' ', $report->status) }}
                                </span>

                                <span id="reportPendingBadge"
                                    class="inline-flex items-center space-x-1 sm:space-x-1.5 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-mono font-medium bg-[#FBF3DB] dark:bg-[#2C2411] text-[#956400] dark:text-[#E9C369] border border-[#956400]/30 shadow-xs {{ $report->status === 'active' ? '' : 'hidden' }}"
                                    title="Laporan belum diproses selama {{ $report->pending_duration }} sejak awal diunggah">
                                    <flux:icon name="clock"
                                        class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-[#956400] dark:text-[#E9C369] shrink-0" />
                                    <span>{{ $report->pending_duration }} belum diproses</span>
                                </span>

                                @auth
                                    @if (Auth::id() === $report->user_id || Auth::user()?->isAdmin())
                                        <div id="creatorStatusActions" class="inline-flex items-center gap-1.5">
                                            @if ($report->status === 'resolved')
                                                <button type="button" onclick="updateReportStatus('active')"
                                                    class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-[#222222] dark:hover:bg-[#2A2A2A] text-slate-700 dark:text-[#EDEDEC] transition flex items-center space-x-1"
                                                    title="Buka kembali laporan ini">
                                                    <flux:icon name="arrow-path" class="w-3 h-3" />
                                                    <span>Buka Kembali</span>
                                                </button>
                                            @else
                                                <button type="button" onclick="updateReportStatus('resolved')"
                                                    class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition flex items-center space-x-1 shadow-xs"
                                                    title="Tandai masalah telah terselesaikan">
                                                    <flux:icon name="check" class="w-3 h-3 text-white" />
                                                    <span>Tandai Selesai (Resolved)</span>
                                                </button>
                                            @endif

                                            <form action="{{ route('reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini secara permanen? Tindakan ini tidak dapat dibatalkan.');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 transition flex items-center space-x-1 border border-rose-200/80 dark:border-rose-900/60"
                                                    title="Hapus laporan ini">
                                                    <flux:icon name="trash" class="w-3 h-3 text-rose-500" />
                                                    <span>Hapus Laporan</span>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                @endauth
                            </div>
                            <span
                                class="text-slate-400 dark:text-[#787774]">{{ $report->created_at->translatedFormat('d M Y, H:i') }}</span>
                        </div>

                        <!-- Judul Laporan -->
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-[#EDEDEC] leading-tight">
                            {{ $report->title }}
                        </h1>

                        <!-- Pelapor -->
                        <div
                            class="flex items-center space-x-2 text-xs text-slate-500 dark:text-[#888888] pb-2 border-b border-slate-100 dark:border-[#222222] flex-wrap gap-y-1.5">
                            <span>Dilaporkan oleh</span>
                            <div class="inline-flex items-center space-x-1 font-bold text-slate-800 dark:text-[#EDEDEC]">
                                <span>@<span>{{ $report->user->username ?? 'anon' }}</span></span>
                                @if ($report->user)
                                    <x-verified-badge :user="$report->user" size="sm" />
                                @endif
                            </div>
                            @auth
                                @if (Auth::id() === $report->user_id)
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800/60">
                                        Laporan Anda
                                    </span>
                                @endif
                            @endauth
                        </div>

                        <!-- Deskripsi Lengkap (dengan Tag/Mention Formatting) -->
                        @php
                            $formattedDescription = preg_replace_callback('/(^|[^a-zA-Z0-9_])@([a-zA-Z0-9_]+)/', function($m) {
                                $u = $m[2];
                                $isAi = strtolower($u) === 'sira';
                                $targetUser = \App\Models\User::where('username', $u)->first();
                                $badgeType = $isAi ? null : ($targetUser ? $targetUser->badgeType() : null);

                                $badgeSvg = '';
                                if ($badgeType === 'admin') {
                                    $badgeSvg = '<svg class="w-3.5 h-3.5 text-amber-500 fill-current shrink-0" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.516 2.17a.75.75 0 0 0-1.032 0 11.209 11.209 0 0 1-7.877 3.08.75.75 0 0 0-.722.515A12.74 12.74 0 0 0 2.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.749.749 0 0 0 .374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.39-.223-2.73-.635-3.985a.75.75 0 0 0-.722-.516l-.143.001c-2.996 0-5.717-1.17-7.734-3.08Zm3.094 8.016a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg>';
                                } elseif ($badgeType === 'verified') {
                                    $badgeSvg = '<svg class="w-3.5 h-3.5 text-sky-500 fill-current shrink-0" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg>';
                                }

                                if ($isAi) {
                                    $cls = 'font-bold text-indigo-700 dark:text-indigo-200 bg-indigo-100/90 dark:bg-indigo-900/60 border border-indigo-300/80 dark:border-indigo-700/80 px-2 py-0.5 rounded-lg shadow-2xs inline-flex items-center gap-1 align-baseline';
                                } elseif ($badgeType === 'admin') {
                                    $cls = 'font-bold text-amber-900 dark:text-amber-200 bg-amber-100/90 dark:bg-amber-950/60 border border-amber-300/80 dark:border-amber-700/80 px-2 py-0.5 rounded-lg shadow-2xs inline-flex items-center gap-1 align-baseline';
                                } elseif ($badgeType === 'verified') {
                                    $cls = 'font-bold text-sky-900 dark:text-sky-200 bg-sky-100/90 dark:bg-sky-950/60 border border-sky-300/80 dark:border-sky-700/80 px-2 py-0.5 rounded-lg shadow-2xs inline-flex items-center gap-1 align-baseline';
                                } else {
                                    $cls = 'font-bold text-emerald-800 dark:text-emerald-200 bg-emerald-100/90 dark:bg-emerald-950/60 border border-emerald-300/80 dark:border-emerald-700/80 px-2 py-0.5 rounded-lg shadow-2xs inline-flex items-center gap-1 align-baseline';
                                }

                                return $m[1] . '<span class="' . $cls . '">@' . $u . $badgeSvg . '</span>';
                            }, e($report->description));
                        @endphp
                        <div
                            class="text-xs sm:text-sm text-slate-700 dark:text-[#CCCCCC] leading-relaxed whitespace-pre-line">
                            {!! $formattedDescription !!}
                        </div>

                        <!-- Lokasi Administratif -->
                        <div
                            class="p-3.5 rounded-2xl bg-slate-50 dark:bg-[#181818] border border-slate-200 dark:border-[#282828] text-xs space-y-1">
                            <div class="font-bold text-slate-800 dark:text-[#EDEDEC] flex items-center space-x-1.5">
                                <flux:icon name="map-pin" class="w-3.5 h-3.5 text-slate-500 shrink-0" />
                                <span>{{ $report->district && strcasecmp($report->district, $report->city ?? '') !== 0 ? $report->district . ', ' : '' }}{{ $report->city ?? $report->district ?? 'Lokasi Terdaftar' }}</span>
                            </div>
                            <p class="text-slate-500 dark:text-[#888888] text-[11px] leading-relaxed">
                                {{ $report->formatted_address ?? 'Koordinat: ' . $report->latitude . ', ' . $report->longitude }}
                            </p>
                        </div>
                    </div>

                    <!-- Box Voting (Like & Dislike) Interaktif -->
                    <div
                        class="p-4 rounded-2xl bg-slate-900 dark:bg-[#111111] dark:border dark:border-[#262626] text-white space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs text-slate-400 dark:text-[#888888] font-medium">Skor Dukungan Warga
                                </div>
                                <div class="text-2xl font-extrabold text-emerald-400" id="voteScoreDisplay">
                                    {{ $report->vote_score }} <span
                                        class="text-xs text-slate-400 dark:text-[#888888] font-normal">poin</span>
                                </div>
                            </div>

                            <!-- Tombol Like & Dislike -->
                            @auth
                                <div class="flex items-center space-x-2">
                                    <!-- Tombol Like (Upvote) -->
                                    <button type="button" onclick="castVote(1)" id="btnUpvote"
                                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 {{ ($userVote && $userVote->value === 1) ? 'bg-emerald-500 text-slate-950' : 'bg-white/10 hover:bg-white/20 dark:bg-[#222222] dark:hover:bg-[#2A2A2A] text-white' }}">
                                        <flux:icon name="hand-thumb-up" class="w-3.5 h-3.5 shrink-0" />
                                        <span id="upvotesCount">{{ $report->upvotes_count }}</span>
                                    </button>

                                    <!-- Tombol Dislike (Downvote) -->
                                    <button type="button" onclick="castVote(-1)" id="btnDownvote"
                                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 {{ ($userVote && $userVote->value === -1) ? 'bg-rose-500 text-white' : 'bg-white/10 hover:bg-white/20 dark:bg-[#222222] dark:hover:bg-[#2A2A2A] text-white' }}">
                                        <flux:icon name="hand-thumb-down" class="w-3.5 h-3.5 shrink-0" />
                                        <span id="downvotesCount">{{ $report->downvotes_count }}</span>
                                    </button>
                                </div>
                            @else
                                <div class="text-right">
                                    <a href="{{ route('login') }}"
                                        class="text-xs text-emerald-400 hover:underline font-semibold">
                                        Masuk untuk vote &rarr;
                                    </a>
                                </div>
                            @endauth
                        </div>
                        <p
                            class="text-[11px] text-slate-400 dark:text-[#888888] border-t border-slate-800 dark:border-[#222222] pt-2">
                            Vote berfungsi menaikkan ranking postingan ke <strong>Urgent & Critical Tier</strong> agar
                            segera diprioritaskan.
                        </p>
                    </div>

                    <!-- Tombol Bagikan Kartu OpenGraph Canvas -->
                    <button type="button" onclick="openOgCanvasModal()"
                        class="w-full py-2.5 px-4 rounded-2xl text-xs font-bold border border-slate-200 dark:border-[#282828] bg-white dark:bg-[#181818] hover:bg-slate-50 dark:hover:bg-[#202020] text-slate-800 dark:text-[#EDEDEC] transition flex items-center justify-center space-x-2 shadow-xs cursor-pointer">
                        <flux:icon name="share" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                        <span>Bagikan & Unduh Kartu (Canvas)</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Bagian Khusus: Multi-Masalah di Titik Koordinat yang Sama -->
        @if (isset($totalCoLocatedCount) && $totalCoLocatedCount > 0)
            <div id="multi-issues" class="bg-white dark:bg-[#141414] p-4 sm:p-6 lg:p-8 rounded-3xl border border-violet-200/80 dark:border-violet-900/60 shadow-sm space-y-5 sm:space-y-6 scroll-mt-24">
                <!-- Header Bagian Multi-Masalah -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 pb-4 border-b border-slate-100 dark:border-[#222222]">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center gap-1.5 px-2 sm:px-2.5 py-0.5 rounded-full text-[11px] sm:text-xs font-semibold bg-violet-100 text-violet-900 dark:bg-violet-950/70 dark:text-violet-200 border border-violet-300/80 dark:border-violet-800/80 shadow-xs">
                                <flux:icon name="squares-2x2" class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-violet-700 dark:text-violet-300 shrink-0" />
                                <span>Multi-Masalah Terdeteksi</span>
                                <span class="inline-flex items-center justify-center px-1.5 rounded-full text-[9px] sm:text-[10px] font-mono font-bold bg-violet-200 text-violet-900 dark:bg-violet-900 dark:text-violet-100">
                                    {{ $report->total_location_issues }} Masalah
                                </span>
                            </span>
                            <span class="text-[11px] sm:text-xs font-mono text-slate-400 dark:text-[#787774]">
                                Titik: {{ $report->latitude }}, {{ $report->longitude }}
                            </span>
                        </div>
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-[#EDEDEC]">
                            Permasalahan Lain di Lokasi yang Sama
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-[#888888]">
                            Terdapat {{ $totalCoLocatedCount }} laporan publik lainnya yang tercatat pada titik koordinat yang sama persis.
                        </p>
                    </div>

                    <!-- Filter Khusus Di-Scope untuk Lokasi Ini -->
                    <div class="flex items-center gap-1 sm:gap-1.5 flex-wrap font-mono text-xs">
                        <span class="text-slate-400 dark:text-[#787774] text-[10px] sm:text-[11px] mr-0.5 sm:mr-1">Filter:</span>
                        <a href="{{ request()->fullUrlWithQuery(['co_filter' => null, 'co_page' => null]) }}#multi-issues"
                           class="px-2 sm:px-2.5 py-1 rounded-lg text-[10px] sm:text-[11px] transition shrink-0 {{ !request('co_filter') ? 'bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111] font-bold' : 'text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-[#202020] hover:bg-slate-200' }}">
                            Semua ({{ $totalCoLocatedCount }})
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['co_filter' => 'urgent', 'co_page' => null]) }}#multi-issues"
                           class="px-2 sm:px-2.5 py-1 rounded-lg text-[10px] sm:text-[11px] transition shrink-0 inline-flex items-center gap-1 {{ request('co_filter') === 'urgent' ? 'bg-amber-600 text-white font-bold' : 'text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 hover:bg-amber-100' }}">
                            <flux:icon name="exclamation-triangle" class="w-2.5 h-2.5 sm:w-3 sm:h-3 shrink-0" />
                            <span>Mendesak<span class="hidden sm:inline"> / Urgent</span></span>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['co_filter' => 'active', 'co_page' => null]) }}#multi-issues"
                           class="px-2 sm:px-2.5 py-1 rounded-lg text-[10px] sm:text-[11px] transition shrink-0 {{ request('co_filter') === 'active' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-[#202020] hover:bg-slate-200' }}">
                            ● Aktif
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['co_filter' => 'resolved', 'co_page' => null]) }}#multi-issues"
                           class="px-2 sm:px-2.5 py-1 rounded-lg text-[10px] sm:text-[11px] transition shrink-0 inline-flex items-center gap-1 {{ request('co_filter') === 'resolved' ? 'bg-emerald-600 text-white font-bold' : 'text-emerald-800 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/50 hover:bg-emerald-100' }}">
                            <flux:icon name="check" class="w-2.5 h-2.5 sm:w-3 sm:h-3 shrink-0" />
                            <span>Selesai</span>
                        </a>
                    </div>
                </div>

                <!-- Daftar Laporan Lain di Lokasi Ini -->
                @if ($coLocatedReports->isEmpty())
                    <div class="py-8 text-center text-xs font-mono text-slate-400 dark:text-[#787774]">
                        Tidak ada laporan lain dengan filter "{{ request('co_filter') }}" pada titik lokasi ini.
                    </div>
                @else
                    <!-- Paginasi Atas (Jika lebih dari 1 halaman) -->
                    @if ($coLocatedReports->hasPages())
                        <div class="pb-2 text-xs font-mono">
                            {{ $coLocatedReports->links() }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        @foreach ($coLocatedReports as $coReport)
                            <div class="p-3 sm:p-4 rounded-2xl border border-slate-200 dark:border-[#222222] bg-[#FBFBFA]/70 dark:bg-[#181818]/60 flex flex-col justify-between hover:border-violet-300 dark:hover:border-violet-700 transition space-y-2.5 sm:space-y-3 group">
                                <div class="space-y-2">
                                    <!-- Foto thumbnail & Badges -->
                                    <div class="flex items-start gap-2.5 sm:gap-3">
                                        <img src="{{ $coReport->image_base64 }}" alt="{{ $coReport->title }}" class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl object-cover shrink-0 border border-slate-200 dark:border-[#282828] transition duration-200">
                                        <div class="flex-1 min-w-0 space-y-1">
                                            <div class="flex items-center gap-1 sm:gap-1.5 flex-wrap">
                                                <span class="inline-flex items-center gap-1 px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-medium {{ $coReport->category_meta['badge_class'] }}">
                                                    <flux:icon name="{{ $coReport->category_icon }}" class="w-2.5 h-2.5" />
                                                    <span>{{ $coReport->category_label }}</span>
                                                </span>
                                                @if ($coReport->rank_tier === 'critical')
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] sm:text-[10px] font-mono font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                                                        Kritis
                                                    </span>
                                                @elseif ($coReport->rank_tier === 'urgent')
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] sm:text-[10px] font-mono font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                                        Urgent
                                                    </span>
                                                @endif
                                                @if ($coReport->status === 'resolved')
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] sm:text-[10px] font-mono font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                                        Selesai
                                                    </span>
                                                @else
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] sm:text-[10px] font-mono text-slate-500 dark:text-[#888888] bg-slate-100 dark:bg-[#252525]">
                                                        Aktif
                                                    </span>
                                                @endif
                                            </div>
                                            <h4 class="text-xs font-bold text-slate-900 dark:text-[#EDEDEC] line-clamp-2 leading-snug group-hover:underline underline-offset-2">
                                                <a href="{{ route('reports.show', $coReport) }}">
                                                    {{ $coReport->title }}
                                                </a>
                                            </h4>
                                        </div>
                                    </div>
                                    <p class="text-[11px] text-slate-500 dark:text-[#888888] line-clamp-2 leading-relaxed">
                                        {{ $coReport->description }}
                                    </p>
                                </div>

                                <div class="pt-2 sm:pt-2.5 border-t border-slate-200/70 dark:border-[#282828] flex items-center justify-between text-[10px] sm:text-[11px] font-mono">
                                    <div class="flex items-center space-x-2 sm:space-x-3 text-slate-500">
                                        <span class="font-bold text-slate-800 dark:text-[#EDEDEC]">{{ $coReport->vote_score }} votes</span>
                                        <span>&bull;</span>
                                        <span>{{ $coReport->comments_count }} komentar</span>
                                    </div>
                                    <a href="{{ route('reports.show', $coReport) }}" class="inline-flex items-center gap-1 text-[11px] sm:text-xs font-semibold text-violet-700 dark:text-violet-400 hover:underline">
                                        <span>Buka</span>
                                        <span>&rarr;</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Paginasi Bawah (Jika lebih dari 1 halaman) -->
                    @if ($coLocatedReports->hasPages())
                        <div class="pt-4 border-t border-slate-100 dark:border-[#222222] text-xs font-mono">
                            {{ $coLocatedReports->links() }}
                        </div>
                    @endif
                @endif
            </div>
        @endif

        <!-- Peta Lokasi Masalah (OpenFreeMap) -->
        <div
            class="bg-white dark:bg-[#141414] p-4 sm:p-6 lg:p-8 rounded-3xl border border-slate-200 dark:border-[#222222] shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-[#EDEDEC]">Titik Koordinat Peta</h3>
                    <p class="text-xs text-slate-500 dark:text-[#888888]">Koordinat: {{ $report->latitude }},
                        {{ $report->longitude }}</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('heatmap.index', ['lat' => $report->latitude, 'lng' => $report->longitude, 'report_id' => $report->id]) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-900/50 transition shadow-2xs">
                        <flux:icon name="fire" class="w-3.5 h-3.5 text-rose-600 dark:text-rose-400 shrink-0" />
                        <span>Buka di Heatmap &rarr;</span>
                    </a>
                    <a href="https://www.openstreetmap.org/?mlat={{ $report->latitude }}&mlon={{ $report->longitude }}#map=17/{{ $report->latitude }}/{{ $report->longitude }}"
                        target="_blank"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white border border-slate-200 dark:border-[#282828] bg-white dark:bg-[#181818] hover:bg-slate-50 dark:hover:bg-[#202020] transition shadow-2xs">
                        <span>Buka di OSM</span>
                        <flux:icon name="arrow-top-right-on-square" class="w-3 h-3 text-slate-400 shrink-0" />
                    </a>
                </div>
            </div>
            <div id="reportMap"
                class="w-full h-72 rounded-2xl border border-slate-200 dark:border-[#282828] overflow-hidden"></div>
        </div>

        <!-- Diskusi & Komentar Bertingkat (Nested Comments) -->
        <div
            class="bg-white dark:bg-[#141414] p-4 sm:p-6 lg:p-8 rounded-3xl border border-slate-200 dark:border-[#222222] shadow-sm space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-[#222222]">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-[#EDEDEC]">Diskusi & Respon Warga</h3>
                    <p class="text-xs text-slate-500 dark:text-[#888888]">Ada <span
                            id="commentsCountDisplay">{{ $report->comments_count }}</span> komentar pada laporan ini</p>
                </div>
            </div>

            <!-- Form Tambah Komentar Utama -->
            @auth
                <form id="mainCommentForm" action="{{ route('comments.store', $report, false) }}" method="POST"
                    class="space-y-3" onsubmit="submitCommentAjax(event, this, null)">
                    @csrf
                    <div class="mention-highlighter-wrapper mention-highlighter-main relative w-full rounded-2xl border border-slate-300 dark:border-[#282828] bg-white dark:bg-[#181818] focus-within:ring-2 focus-within:ring-emerald-500 focus-within:border-emerald-500 overflow-hidden transition">
                        <div class="mention-backdrop absolute inset-0 pointer-events-none px-4 py-3 text-sm font-sans leading-relaxed text-transparent overflow-hidden select-none whitespace-pre-wrap break-words" aria-hidden="true"></div>
                        <textarea name="content" rows="3" required
                            placeholder="Tulis komentar atau tanggapan terkait masalah ini (Tag @Sira untuk meminta bantuan AI)..."
                            class="mention-input relative z-10 w-full px-4 py-3 bg-transparent text-slate-900 dark:text-[#EDEDEC] placeholder-slate-400 dark:placeholder-[#666666] text-sm font-sans leading-relaxed focus:outline-none resize-y block border-0 ring-0 focus:ring-0"></textarea>
                    </div>

                    <div class="flex items-center justify-between flex-wrap gap-2 pt-1">
                        <div
                            class="flex items-center space-x-1.5 text-xs text-indigo-700 dark:text-indigo-300 font-medium bg-indigo-50 dark:bg-indigo-950/40 px-3 py-1.5 rounded-xl border border-indigo-100 dark:border-indigo-900/50">
                            <flux:icon name="sparkles" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400 shrink-0" />
                            <span>Tip: Tag <strong>@Sira</strong> di komentar untuk meminta bantuan atau ringkasan AI</span>
                        </div>
                        <button type="submit"
                            class="submit-btn px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-sm transition flex items-center space-x-1.5">
                            <span>Kirim Komentar</span>
                        </button>
                    </div>
                </form>
            @else
                <div
                    class="p-4 rounded-2xl bg-slate-50 dark:bg-[#181818] border border-slate-200 dark:border-[#282828] text-center text-xs text-slate-600 dark:text-[#999999]">
                    Silakan <a href="{{ route('login') }}"
                        class="font-bold text-emerald-700 dark:text-emerald-400 hover:underline">Masuk</a> untuk ikut berdiskusi
                    dan memberikan tanggapan.
                </div>
            @endauth

            <!-- Daftar Komentar (Pohon Bertingkat) -->
            <div class="space-y-5 pt-4" id="commentsContainer">
                <p id="emptyCommentsMsg"
                    class="text-xs text-slate-400 dark:text-[#787774] text-center py-6 {{ $report->rootComments->isEmpty() ? '' : 'hidden' }}">
                    Belum ada komentar. Jadilah yang pertama berkomentar!
                </p>
                <div id="commentsList" class="space-y-5">
                    @foreach ($report->rootComments as $comment)
                        @include('reports._comment_item', ['comment' => $comment])
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Modal OpenGraph Canvas Generator & Export -->
        <div id="ogCanvasModal"
            class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/70 backdrop-blur-xs flex items-center justify-center p-4"
            onclick="if (event.target === this) closeOgCanvasModal();">
            <div class="relative w-full max-w-4xl bg-white dark:bg-[#141414] rounded-3xl border border-slate-200 dark:border-[#262626] shadow-2xl p-6 sm:p-8 space-y-6 animate-in fade-in zoom-in-95 duration-200"
                onclick="event.stopPropagation()">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-[#222222]">
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2">
                            <span
                                class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                Dynamic OpenGraph
                            </span>
                            <span class="text-xs text-slate-400 dark:text-[#777777] font-mono">1200 &times; 630 px</span>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900 dark:text-[#EDEDEC]">
                            Kartu Laporan Interaktif (HTML5 Canvas)
                        </h3>
                    </div>
                    <button type="button" onclick="closeOgCanvasModal()"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-white bg-slate-100 dark:bg-[#202020] transition cursor-pointer">
                        &times;
                    </button>
                </div>

                <!-- Canvas Container -->
                <div class="space-y-3">
                    <div
                        class="relative w-full bg-[#0E0E0E] rounded-2xl overflow-hidden border border-slate-200 dark:border-[#262626] shadow-inner flex items-center justify-center p-2 sm:p-4">
                        <canvas id="ogCardCanvas" width="1200" height="630"
                            class="w-full h-auto max-h-[480px] rounded-xl object-contain shadow-md"></canvas>
                        <div id="canvasLoadingOverlay"
                            class="absolute inset-0 bg-[#0E0E0E]/80 flex flex-col items-center justify-center space-y-2 text-white text-xs font-mono">
                            <div class="w-6 h-6 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin">
                            </div>
                            <span>Merender Kartu Canvas...</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-[#888888] flex items-center justify-between">
                        <span>Resolusi standar media sosial (1.91:1) siap dibagikan ke WhatsApp, Twitter/X, Telegram, atau
                            Instagram.</span>
                        <span id="canvasRenderStatus" class="text-emerald-600 dark:text-emerald-400 font-medium"></span>
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-slate-100 dark:border-[#222222]">
                    <!-- Unduh PNG -->
                    <button type="button" onclick="downloadOgCanvas()"
                        class="w-full py-3 px-4 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition flex items-center justify-center space-x-2 shadow-xs cursor-pointer">
                        <flux:icon name="arrow-down-tray" class="w-4 h-4 text-white" />
                        <span>Unduh Gambar (PNG)</span>
                    </button>

                    <!-- Salin Gambar ke Clipboard -->
                    <button type="button" onclick="copyOgCanvasToClipboard()" id="btnCopyImage"
                        class="w-full py-3 px-4 rounded-xl text-xs font-bold bg-slate-900 hover:bg-black dark:bg-[#202020] dark:hover:bg-[#282828] text-white transition flex items-center justify-center space-x-2 cursor-pointer">
                        <flux:icon name="clipboard-document" class="w-4 h-4 text-white" />
                        <span id="copyImageText">Salin Gambar (Clipboard)</span>
                    </button>

                    <!-- Salin Tautan OpenGraph -->
                    <button type="button" onclick="copyReportUrl()" id="btnCopyUrl"
                        class="w-full py-3 px-4 rounded-xl text-xs font-bold border border-slate-200 dark:border-[#282828] bg-white dark:bg-[#181818] hover:bg-slate-50 dark:hover:bg-[#222222] text-slate-800 dark:text-[#EDEDEC] transition flex items-center justify-center space-x-2 cursor-pointer">
                        <flux:icon name="share" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                        <span id="copyUrlText">Salin Tautan Laporan</span>
                    </button>
                </div>

                <!-- Direct URL Link & Crawler Info -->
                <div
                    class="p-3 rounded-xl bg-slate-50 dark:bg-[#181818] border border-slate-200 dark:border-[#242424] flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-2 text-slate-600 dark:text-[#999999] truncate mr-2">
                        <flux:icon name="photo" class="w-3.5 h-3.5 shrink-0 text-slate-400" />
                        <span class="font-mono text-[11px] truncate">URL Server:
                            {{ route('reports.ogImage', $report) }}</span>
                    </div>
                    <a href="{{ route('reports.ogImage', $report) }}" target="_blank"
                        class="shrink-0 text-emerald-600 dark:text-emerald-400 hover:underline font-semibold text-[11px]">
                        Buka Gambar Langsung &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // -------------------------------------------------------------
        // Inisialisasi Peta Lokasi Laporan (OpenFreeMap)
        // -------------------------------------------------------------
        const reportLat = {{ $report->latitude }};
        const reportLng = {{ $report->longitude }};

        const map = new maplibregl.Map({
            container: 'reportMap',
            style: 'https://tiles.openfreemap.org/styles/bright',
            center: [reportLng, reportLat],
            zoom: 15
        });

        // Tambahkan Marker Pin
        const heatmapUrl = "{{ route('heatmap.index', ['lat' => $report->latitude, 'lng' => $report->longitude, 'report_id' => $report->id]) }}";
        const popupContent = `
            <div class="p-1 space-y-1 font-sans">
                <strong class="text-xs text-slate-900 dark:text-[#EDEDEC] block leading-tight">{{ addslashes($report->title) }}</strong>
                <a href="${heatmapUrl}" class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 hover:text-rose-700 hover:underline">
                    Buka Titik di Heatmap &rarr;
                </a>
            </div>
        `;

        new maplibregl.Marker({ color: '#E11D48' })
            .setLngLat([reportLng, reportLat])
            .setPopup(new maplibregl.Popup({ offset: 12 }).setHTML(popupContent))
            .addTo(map);

        // -------------------------------------------------------------
        // Fungsi Toggle Balas Komentar
        // -------------------------------------------------------------
        function toggleReplyForm(commentId, targetUsername = '') {
            const form = document.getElementById('reply-form-' + commentId);
            if (form) {
                form.classList.toggle('hidden');
                if (!form.classList.contains('hidden')) {
                    const ta = form.querySelector('textarea');
                    if (ta) {
                        const target = targetUsername || ta.getAttribute('data-target-user') || '';
                        if (target && !ta.value.trim()) {
                            ta.value = `@${target} `;
                        }
                        ta.focus();
                        ta.setSelectionRange(ta.value.length, ta.value.length);
                        if (typeof updateMentionHighlights === 'function') {
                            updateMentionHighlights(ta);
                        }
                    }
                }
            }
        }

        // -------------------------------------------------------------
        // Voting AJAX Interaktif (Like & Dislike)
        // -------------------------------------------------------------
        function castVote(val) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const btnUpvote = document.getElementById('btnUpvote');
            const btnDownvote = document.getElementById('btnDownvote');

            fetch("{{ route('reports.vote', $report, false) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ value: val })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Perbarui angka counter
                        document.getElementById('voteScoreDisplay').innerHTML = `${data.vote_score} <span class="text-xs text-slate-400 font-normal">poin</span>`;
                        document.getElementById('upvotesCount').innerText = data.upvotes_count;
                        document.getElementById('downvotesCount').innerText = data.downvotes_count;

                        // Perbarui status warna tombol
                        if (data.user_vote === 1) {
                            btnUpvote.className = 'px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 bg-emerald-500 text-slate-950';
                            btnDownvote.className = 'px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 bg-white/10 hover:bg-white/20 text-white';
                        } else if (data.user_vote === -1) {
                            btnUpvote.className = 'px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 bg-white/10 hover:bg-white/20 text-white';
                            btnDownvote.className = 'px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 bg-rose-500 text-white';
                        } else {
                            btnUpvote.className = 'px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 bg-white/10 hover:bg-white/20 text-white';
                            btnDownvote.className = 'px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 bg-white/10 hover:bg-white/20 text-white';
                        }

                        // Perbarui badge rank_tier jika naik/turun
                        updateTierBadge(data.rank_tier);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Gagal memperbarui vote. Silakan coba lagi.');
                });
        }

        function updateTierBadge(tier) {
            const container = document.getElementById('tierBadgeContainer');
            if (!container) return;

            if (tier === 'critical') {
                container.innerHTML = `<span class="inline-flex items-center space-x-1.5 px-3.5 py-1 rounded-full text-xs font-black bg-rose-600 text-white shadow-lg"><span class="w-2 h-2 rounded-full bg-white animate-ping"></span><span>CRITICAL TIER</span></span>`;
            } else if (tier === 'urgent') {
                container.innerHTML = `<span class="inline-flex items-center space-x-1.5 px-3.5 py-1 rounded-full text-xs font-bold bg-amber-500 text-white shadow-md"><svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M8 2l6.5 11.5H1.5L8 2zM8 6.5v3M8 12v.5"/></svg><span>URGENT TIER</span></span>`;
            } else if (tier === 'trending') {
                container.innerHTML = `<span class="inline-flex items-center space-x-1.5 px-3.5 py-1 rounded-full text-xs font-bold bg-teal-600 text-white shadow-md"><svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M8 1.5c.8 2-1 3.5-1 5 0 2 1.5 3 2.5 2 0 2-1 4-3 5 4 0 6-3 6-6 0-3-2-5-3-6-1.5 2-1.5-1-1.5 0z"/></svg><span>TRENDING TIER</span></span>`;
            } else {
                container.innerHTML = `<span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-semibold bg-slate-800/80 backdrop-blur-md text-white">NORMAL TIER</span>`;
            }
        }

        // -------------------------------------------------------------
        // Update Status Laporan (Khusus Pembuat Post / Author)
        // -------------------------------------------------------------
        function updateReportStatus(newStatus) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const badge = document.getElementById('reportStatusBadge');
            const actionsContainer = document.getElementById('creatorStatusActions');

            fetch("{{ route('reports.updateStatus', $report, false) }}", {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ status: newStatus })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const pendingBadge = document.getElementById('reportPendingBadge');
                        const imgPending = document.getElementById('imagePendingBadge');
                        if (data.status === 'resolved') {
                            badge.className = 'px-2.5 py-1 rounded-full font-bold uppercase tracking-wider transition duration-200 bg-emerald-600 text-white shadow-xs';
                            badge.innerText = 'Status: RESOLVED';
                            if (pendingBadge) pendingBadge.classList.add('hidden');
                            if (imgPending) imgPending.classList.add('hidden');
                        } else {
                            badge.className = 'px-2.5 py-1 rounded-full font-bold uppercase tracking-wider transition duration-200 bg-amber-500 text-white shadow-xs';
                            badge.innerText = 'Status: ' + (data.status || 'ACTIVE').toUpperCase();
                            if (pendingBadge) pendingBadge.classList.remove('hidden');
                            if (imgPending) imgPending.classList.remove('hidden');
                        }

                        if (actionsContainer) {
                            const deleteFormHtml = `
                                <form action="{{ route('reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini secara permanen? Tindakan ini tidak dapat dibatalkan.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 transition flex items-center space-x-1 border border-rose-200/80 dark:border-rose-900/60" title="Hapus laporan ini">
                                        <flux:icon name="trash" class="w-3 h-3 text-rose-500" />
                                        <span>Hapus Laporan</span>
                                    </button>
                                </form>
                            `;
                            if (data.status === 'resolved') {
                                actionsContainer.innerHTML = `
                                <button type="button" onclick="updateReportStatus('active')" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-[#222222] dark:hover:bg-[#2A2A2A] dark:text-[#EDEDEC] transition flex items-center space-x-1" title="Buka kembali laporan ini">
                                    <flux:icon name="arrow-path" class="w-3 h-3" />
                                    <span>Buka Kembali</span>
                                </button>
                                ` + deleteFormHtml;
                            } else {
                                actionsContainer.innerHTML = `
                                <button type="button" onclick="updateReportStatus('resolved')" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition flex items-center space-x-1 shadow-xs" title="Tandai masalah telah terselesaikan">
                                    <flux:icon name="check" class="w-3 h-3" />
                                    <span>Tandai Selesai (Resolved)</span>
                                </button>
                                ` + deleteFormHtml;
                            }
                        }
                    } else {
                        alert(data.message || 'Gagal mengubah status laporan.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan saat mengubah status laporan.');
                });
        }

        // -------------------------------------------------------------
        // Fungsi Penyisipan Snippet Formula LaTeX
        // -------------------------------------------------------------
        function insertLatexSnippet(btn, snippet) {
            const form = btn.closest('form');
            if (!form) return;
            const textarea = form.querySelector('textarea[name="content"]');
            if (!textarea) return;

            const start = textarea.selectionStart || 0;
            const end = textarea.selectionEnd || 0;
            const text = textarea.value;
            textarea.value = text.substring(0, start) + snippet + text.substring(end);
            textarea.selectionStart = textarea.selectionEnd = start + snippet.length;
            textarea.focus();
            updateMentionHighlights(textarea);
        }

        // -------------------------------------------------------------
        // Engine Formatting LaTeX (KaTeX) & Markdown (marked)
        // -------------------------------------------------------------
        function formatCommentText(rawText) {
            if (!rawText) return '';

            const mathTokens = [];
            const renderKaTeX = (formula, display) => {
                try {
                    if (window.katex && typeof window.katex.renderToString === 'function') {
                        return window.katex.renderToString(formula.trim(), {
                            displayMode: display,
                            throwOnError: false
                        });
                    }
                } catch (e) {
                    console.warn('KaTeX render error:', e);
                }
                return display ? `$$${formula}$$` : `$${formula}$`;
            };

            // 1. Ekstrak Display Math: $$...$$ atau \[...\]
            let text = rawText.replace(/\$\$([\s\S]+?)\$\$|\\\[([\s\S]+?)\\\]/g, (match, p1, p2) => {
                const token = `%%KATEX_TOKEN_${mathTokens.length}%%`;
                mathTokens.push(renderKaTeX(p1 || p2, true));
                return token;
            });

            // 2. Ekstrak Inline Math: $...$ atau \(...\)
            text = text.replace(/\$([^\$\n]+?)\$|\\\(([^\n]+?)\\\)/g, (match, p1, p2) => {
                const token = `%%KATEX_TOKEN_${mathTokens.length}%%`;
                mathTokens.push(renderKaTeX(p1 || p2, false));
                return token;
            });

            // 3. Deteksi Perintah LaTeX Bare tanpa $: \frac{...}{...}, \sqrt{...}, \sum, \int, dll
            text = text.replace(/(\\(?:frac|sqrt|sum|int|alpha|beta|gamma|theta|pi|infty|pm|times|div|leq|geq|neq|approx)\b[^{$\n]*(?:\{[^{}]*\}[^{$\n]*)*)/g, (match) => {
                const token = `%%KATEX_TOKEN_${mathTokens.length}%%`;
                mathTokens.push(renderKaTeX(match, false));
                return token;
            });

            // 4. Format Markdown (Bold, Italic, List, Code) menggunakan marked
            let html = text;
            if (window.marked && typeof window.marked.parse === 'function') {
                html = window.marked.parse(text, { breaks: true, gfm: true });
            } else {
                // Fallback jika marked belum siap
                html = text
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\*(.*?)\*/g, '<em>$1</em>')
                    .replace(/`([^`]+)`/g, '<code class="px-1 py-0.5 bg-slate-200/70 dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded font-mono text-[11px]">$1</code>')
                    .replace(/\n/g, '<br>');
            }

            // 5. Kembalikan token LaTeX yang sudah dirender sempurna
            mathTokens.forEach((tokenHtml, idx) => {
                html = html.replaceAll(`%%KATEX_TOKEN_${idx}%%`, tokenHtml);
            });

            // 6. Highlight mention username (Sira bot atau user lain) dengan efek highlight mencolok
            const currentAuthUser = "{{ Auth::user()?->username ?? '' }}";
            html = html.replace(/(^|[^a-zA-Z0-9_])\x40([a-zA-Z0-9_]+)/g, (match, prefix, username) => {
                const isAi = username.toLowerCase() === 'sira';
                const isMe = currentAuthUser && username.toLowerCase() === currentAuthUser.toLowerCase();
                let badgeClass = '';
                let iconSvg = '';

                if (isAi) {
                    badgeClass = 'font-bold text-indigo-700 dark:text-indigo-200 bg-indigo-100/90 dark:bg-indigo-900/60 border border-indigo-300/80 dark:border-indigo-700/80 px-2 py-0.5 rounded-lg shadow-xs ring-1 ring-indigo-400/30 transition-all';
                    iconSvg = '<svg class="w-3 h-3 text-indigo-600 dark:text-indigo-300 inline mr-0.5" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a2 2 0 0 1 2 2v1h1a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1V3a2 2 0 0 1 2-2z"/></svg>';
                } else if (isMe) {
                    badgeClass = 'font-bold text-amber-900 dark:text-amber-100 bg-amber-200/90 dark:bg-amber-900/70 border border-amber-400 dark:border-amber-600 px-2 py-0.5 rounded-lg shadow-xs ring-2 ring-amber-400/60 transition-all';
                    iconSvg = '<span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block mr-1"></span>';
                } else {
                    badgeClass = 'font-bold text-emerald-700 dark:text-emerald-200 bg-emerald-100/90 dark:bg-emerald-900/60 border border-emerald-300/80 dark:border-emerald-700/80 px-2 py-0.5 rounded-lg shadow-xs ring-1 ring-emerald-400/30 transition-all';
                    iconSvg = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block mr-1"></span>';
                }

                const badge = `<span class="inline-flex items-center mx-0.5 ${badgeClass}">${iconSvg}@${username}</span>`;
                return prefix + badge;
            });

            return html;
        }

        // Format semua elemen komentar di dalam kontainer
        function formatAllComments(container) {
            const root = container || document.getElementById('commentsContainer');
            if (!root) return;

            root.querySelectorAll('.comment-body').forEach(el => {
                const raw = el.getAttribute('data-raw-content') || el.innerText;
                if (raw) {
                    el.innerHTML = formatCommentText(raw);
                }
            });
        }

        // -------------------------------------------------------------
        // In-Input Realtime Mention Highlighter
        // -------------------------------------------------------------
        function updateMentionHighlights(textarea) {
            if (!textarea || textarea.tagName !== 'TEXTAREA') return;
            const wrapper = textarea.closest('.mention-highlighter-wrapper');
            if (!wrapper) return;
            const backdrop = wrapper.querySelector('.mention-backdrop');
            if (!backdrop) return;

            let text = textarea.value || '';
            let escaped = text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');

            // Highlight bot Sira secara realtime
            escaped = escaped.replace(/(^|[^a-zA-Z0-9_])\x40([sS][iI][rR][aA])\b/g, '$1<mark class="mention-tag mention-tag-sira">&#64;$2</mark>');

            // Highlight username pengguna lain secara realtime
            escaped = escaped.replace(/(^|[^a-zA-Z0-9_])\x40([a-zA-Z0-9_]{2,30})\b/g, function (match, prefix, uname) {
                if (uname.toLowerCase() === 'sira') return match;
                return prefix + '<mark class="mention-tag mention-tag-user">&#64;' + uname + '</mark>';
            });

            if (text.endsWith('\n')) {
                escaped += '&nbsp;';
            }

            backdrop.innerHTML = escaped;
            syncBackdropScroll(textarea);
        }

        function syncBackdropScroll(textarea) {
            if (!textarea || textarea.tagName !== 'TEXTAREA') return;
            const wrapper = textarea.closest('.mention-highlighter-wrapper');
            if (wrapper) {
                const backdrop = wrapper.querySelector('.mention-backdrop');
                if (backdrop) {
                    backdrop.scrollTop = textarea.scrollTop;
                    backdrop.scrollLeft = textarea.scrollLeft;
                }
            }
        }

        // -------------------------------------------------------------
        // Komentar AJAX Cepat (Instant Delivery + Asynchronous AI Bot @Sira)
        // -------------------------------------------------------------
        function submitCommentAjax(event, form, parentId) {
            event.preventDefault();
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const submitBtn = form.querySelector('.submit-btn') || form.querySelector('button[type="submit"]');
            const originalBtnHtml = submitBtn ? submitBtn.innerHTML : 'Kirim';
            const textarea = form.querySelector('textarea[name="content"]');
            const contentVal = textarea ? textarea.value.trim() : '';

            if (!contentVal) return;

            // Ubah status tombol sementara saat pengiriman data komentar (hanya ~30ms)
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<svg class="animate-spin -ml-1 mr-1.5 h-3.5 w-3.5 text-white inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Mengirim...`;
            }

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) {
                        throw new Error(data.message || 'Gagal mengirim komentar.');
                    }
                    return data;
                })
                .then(data => {
                    if (data.success) {
                        // 1. Perbarui jumlah komentar di header
                        if (document.getElementById('commentsCountDisplay')) {
                            document.getElementById('commentsCountDisplay').innerText = data.comments_count;
                        }

                        // 2. Sembunyikan pesan kosong jika ada
                        const emptyMsg = document.getElementById('emptyCommentsMsg');
                        if (emptyMsg) {
                            emptyMsg.classList.add('hidden');
                        }

                        // 3. Masukkan komentar pengguna secara instan tanpa lag!
                        if (parentId) {
                            // Balasan (nested reply)
                            const repliesContainer = document.getElementById('replies-container-' + parentId);
                            if (repliesContainer) {
                                repliesContainer.classList.remove('hidden');
                                repliesContainer.insertAdjacentHTML('beforeend', data.comment_html);
                            }
                            form.reset();
                            const ta = form.querySelector('textarea');
                            const targetUser = ta ? ta.getAttribute('data-target-user') : '';
                            if (ta && targetUser) {
                                ta.value = `@${targetUser} `;
                            }
                            const replyBackdrop = form.querySelector('.mention-backdrop');
                            if (replyBackdrop) replyBackdrop.innerHTML = '';
                            toggleReplyForm(parentId);
                        } else {
                            // Komentar utama (root)
                            const commentsList = document.getElementById('commentsList');
                            if (commentsList) {
                                commentsList.insertAdjacentHTML('afterbegin', data.comment_html);
                            }
                            form.reset();
                            const mainBackdrop = form.querySelector('.mention-backdrop');
                            if (mainBackdrop) mainBackdrop.innerHTML = '';
                        }

                        // Format LaTeX & Markdown pada komentar yang baru diposting
                        const newCommentEl = document.getElementById('comment-' + data.comment_id);
                        if (newCommentEl) {
                            formatAllComments(newCommentEl);
                        }

                        // 4. Jika user mention @Sira, munculkan indikator AI mengetik & fetch balasan asynchronous
                        if (data.has_ai_mention) {
                            const indicatorId = 'ai-typing-' + data.comment_id;
                            const indicatorHtml = `
                            <div id="${indicatorId}" class="flex items-start space-x-3 p-3.5 rounded-2xl bg-gradient-to-r from-indigo-50/70 to-purple-50/70 border border-indigo-200/80 shadow-xs animate-pulse">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-600 text-white font-bold flex items-center justify-center text-sm shrink-0 ring-2 ring-indigo-200">
                                    <svg class="w-4 h-4 text-white" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3" y="4" width="10" height="9" rx="2"/><circle cx="6" cy="8" r="0.75" fill="currentColor"/><circle cx="10" cy="8" r="0.75" fill="currentColor"/><path d="M8 1.5v2.5M6 10.5h4"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs font-bold text-slate-900">@Sira</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-600 text-white">SIRA AI ASSISTANT</span>
                                        <span class="text-[11px] text-indigo-500 font-medium">• Sedang membalas...</span>
                                    </div>
                                    <div class="mt-1.5 flex items-center space-x-2 text-xs text-indigo-800 font-medium">
                                        <span class="inline-block w-2 h-2 rounded-full bg-indigo-600 animate-bounce"></span>
                                        <span class="inline-block w-2 h-2 rounded-full bg-indigo-600 animate-bounce [animation-delay:0.2s]"></span>
                                        <span class="inline-block w-2 h-2 rounded-full bg-indigo-600 animate-bounce [animation-delay:0.4s]"></span>
                                        <span class="ml-1 text-slate-500">SIRA AI sedang menganalisis laporan dan menyiapkan respon...</span>
                                    </div>
                                </div>
                            </div>
                        `;

                            // Tempatkan indikator di dalam replies container
                            const targetReplies = parentId
                                ? document.getElementById('replies-container-' + parentId)
                                : document.getElementById('replies-container-' + data.comment_id);

                            if (targetReplies) {
                                targetReplies.classList.remove('hidden');
                                targetReplies.insertAdjacentHTML('beforeend', indicatorHtml);
                            }

                            // Panggil endpoint balasan AI secara asynchronous di background
                            const aiReplyUrlTemplate = "{{ route('comments.aiReply', [$report, ':commentId'], false) }}";
                            const aiReplyUrl = aiReplyUrlTemplate.replace(':commentId', data.comment_id);

                            fetch(aiReplyUrl, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            })
                                .then(async res => {
                                    const aiData = await res.json();
                                    if (!res.ok) throw new Error(aiData.message || 'Gagal memproses respon AI.');
                                    return aiData;
                                })
                                .then(aiData => {
                                    const indicator = document.getElementById(indicatorId);
                                    if (indicator && aiData.ai_comment_html) {
                                        indicator.insertAdjacentHTML('afterend', aiData.ai_comment_html);
                                        indicator.remove();

                                        const aiEl = document.getElementById('comment-' + aiData.ai_comment_id);
                                        if (aiEl) {
                                            formatAllComments(aiEl);
                                        }
                                    }
                                    if (document.getElementById('commentsCountDisplay') && aiData.comments_count) {
                                        document.getElementById('commentsCountDisplay').innerText = aiData.comments_count;
                                    }
                                })
                                .catch(err => {
                                    console.error('AI reply error:', err);
                                    const indicator = document.getElementById(indicatorId);
                                    if (indicator) {
                                        indicator.className = 'p-3 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center space-x-2';
                                        indicator.innerHTML = `<svg class="w-4 h-4 text-amber-600 shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M8 2l6.5 11.5H1.5L8 2z"/><path d="M8 6.5v3M8 12v.5"/></svg><span>AI @Sira sedang tidak dapat merespons saat ini. Komentar Anda tetap tersimpan.</span>`;
                                        setTimeout(() => indicator.remove(), 4500);
                                    }
                                });
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert(err.message || 'Terjadi kesalahan saat memposting komentar.');
                })
                .finally(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHtml;
                    }
                });
        }

        // -------------------------------------------------------------
        // Hapus Komentar AJAX
        // -------------------------------------------------------------
        function deleteCommentAjax(event, form, commentId) {
            event.preventDefault();
            if (!confirm('Hapus komentar ini?')) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) {
                        throw new Error(data.message || 'Gagal menghapus komentar.');
                    }
                    return data;
                })
                .then(data => {
                    if (data.success) {
                        const commentEl = document.getElementById('comment-' + commentId);
                        if (commentEl) {
                            commentEl.classList.add('opacity-0', 'transition', 'duration-300');
                            setTimeout(() => {
                                commentEl.remove();
                                // Cek apakah list komentar kosong
                                const commentsList = document.getElementById('commentsList');
                                const emptyMsg = document.getElementById('emptyCommentsMsg');
                                if (commentsList && commentsList.children.length === 0 && emptyMsg) {
                                    emptyMsg.classList.remove('hidden');
                                }
                            }, 300);
                        }

                        if (document.getElementById('commentsCountDisplay')) {
                            document.getElementById('commentsCountDisplay').innerText = data.comments_count;
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert(err.message || 'Gagal menghapus komentar.');
                });
        }

        // -------------------------------------------------------------
        // Auto-Complete Mention (@) Sistem
        // -------------------------------------------------------------
        (function initMentionSystem() {
            // Buat elemen dropdown mention di body
            const dropdown = document.createElement('div');
            dropdown.id = 'mentionDropdown';
            dropdown.style.zIndex = '99999';
            dropdown.className = 'fixed hidden bg-white dark:bg-[#161615] border border-slate-200 dark:border-[#262626] rounded-2xl shadow-2xl overflow-hidden w-72 max-w-[90vw] transition-opacity duration-150 text-left';
            dropdown.innerHTML = `
                <div class="px-3 py-2 bg-slate-50 dark:bg-[#1F1F1E] border-b border-slate-100 dark:border-[#282828] text-[10px] font-bold text-slate-400 dark:text-[#888888] uppercase tracking-wider flex items-center justify-between">
                    <span>Saran Akun & AI</span>
                    <span class="text-[9px] font-normal lowercase text-slate-400 dark:text-[#777777]">Gunakan ↑↓ dan ↵</span>
                </div>
                <div id="mentionDropdownList" class="p-1 max-h-56 overflow-y-auto space-y-0.5"></div>
            `;
            document.body.appendChild(dropdown);

            const dropdownList = document.getElementById('mentionDropdownList');
            let currentTargetTextarea = null;
            let mentionStartIndex = -1;
            let mentionQuery = '';
            let currentUsers = [];
            let highlightedIndex = 0;

            function closeMentionDropdown() {
                dropdown.classList.add('hidden');
                dropdown.style.display = 'none';
                currentUsers = [];
                highlightedIndex = 0;
                currentTargetTextarea = null;
            }

            function renderSuggestions() {
                if (!currentUsers || currentUsers.length === 0) {
                    closeMentionDropdown();
                    return;
                }

                dropdownList.innerHTML = '';
                currentUsers.forEach((user, index) => {
                    const item = document.createElement('div');
                    const isSelected = (index === highlightedIndex);
                    item.className = isSelected
                        ? 'px-3 py-2 rounded-xl text-xs cursor-pointer flex items-center justify-between transition bg-indigo-600 text-white font-semibold shadow-xs'
                        : 'px-3 py-2 rounded-xl text-xs cursor-pointer flex items-center justify-between transition hover:bg-slate-100 dark:hover:bg-[#222222] text-slate-800 dark:text-[#EDEDEC]';

                    const isAi = user.is_ai;
                    let badgeMarkup = '';
                    if (isAi) {
                        badgeMarkup = `<span class="ml-2 shrink-0 px-2 py-0.5 rounded-full text-[9px] font-extrabold ${isSelected ? 'bg-white/20 text-white' : 'bg-indigo-600 text-white'}">SIRA AI</span>`;
                    } else if (user.badge_type === 'admin') {
                        badgeMarkup = `<span class="ml-2 shrink-0 inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[9px] font-bold ${isSelected ? 'bg-amber-400 text-slate-950' : 'bg-amber-100 text-amber-900 dark:bg-amber-950/80 dark:text-amber-300'}"><svg class="w-3 h-3 text-amber-500 fill-current shrink-0" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.516 2.17a.75.75 0 0 0-1.032 0 11.209 11.209 0 0 1-7.877 3.08.75.75 0 0 0-.722.515A12.74 12.74 0 0 0 2.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.749.749 0 0 0 .374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.39-.223-2.73-.635-3.985a.75.75 0 0 0-.722-.516l-.143.001c-2.996 0-5.717-1.17-7.734-3.08Zm3.094 8.016a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg><span>ADMIN</span></span>`;
                    } else if (user.badge_type === 'verified') {
                        badgeMarkup = `<span class="ml-2 shrink-0 inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[9px] font-bold ${isSelected ? 'bg-sky-400 text-slate-950' : 'bg-sky-100 text-sky-900 dark:bg-sky-950/80 dark:text-sky-300'}"><svg class="w-3 h-3 text-sky-500 fill-current shrink-0" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg><span>VERIFIED</span></span>`;
                    }

                    item.innerHTML = `
                        <div class="flex items-center space-x-2 min-w-0">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 ${isAi ? 'bg-white text-indigo-700 shadow-xs' : 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 uppercase'}">
                                ${isAi ? '<svg class="w-3.5 h-3.5 text-indigo-600" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a2 2 0 0 1 2 2v1h1a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1V3a2 2 0 0 1 2-2z"/></svg>' : (user.username ? user.username.charAt(0).toUpperCase() : 'U')}
                            </div>
                            <div class="truncate">
                                <div class="truncate text-xs ${isSelected ? 'text-white' : (isAi ? 'text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-900 dark:text-[#EDEDEC] font-medium')}">${user.name}</div>
                                <div class="text-[11px] ${isSelected ? 'text-indigo-100' : 'text-slate-400 dark:text-[#888888]'} font-mono">@${user.username}</div>
                            </div>
                        </div>
                        ${badgeMarkup}
                    `;

                    item.addEventListener('mousedown', (e) => {
                        e.preventDefault();
                        selectUser(user);
                    });

                    item.addEventListener('mouseenter', () => {
                        highlightedIndex = index;
                        updateHighlightStyles();
                    });

                    dropdownList.appendChild(item);
                });

                dropdown.classList.remove('hidden');
                dropdown.style.display = 'block';
                positionDropdown();
            }

            function updateHighlightStyles() {
                const items = dropdownList.children;
                for (let i = 0; i < items.length; i++) {
                    const isSelected = (i === highlightedIndex);
                    items[i].className = isSelected
                        ? 'px-3 py-2 rounded-xl text-xs cursor-pointer flex items-center justify-between transition bg-indigo-600 text-white font-semibold shadow-xs'
                        : 'px-3 py-2 rounded-xl text-xs cursor-pointer flex items-center justify-between transition hover:bg-slate-100 dark:hover:bg-[#222222] text-slate-800 dark:text-[#EDEDEC]';
                }
            }

            function positionDropdown() {
                if (!currentTargetTextarea) return;
                const rect = currentTargetTextarea.getBoundingClientRect();
                const dropdownHeight = dropdown.offsetHeight || 220;

                // Karena menggunakan fixed, koordinat relative langsung terhadap viewport
                let top = rect.bottom + 6;
                if (top + dropdownHeight > window.innerHeight && rect.top - dropdownHeight - 6 > 0) {
                    top = rect.top - dropdownHeight - 6;
                }

                const left = Math.max(16, Math.min(rect.left, window.innerWidth - 320));

                dropdown.style.top = Math.round(top) + 'px';
                dropdown.style.left = Math.round(left) + 'px';
            }

            function selectUser(user) {
                if (!currentTargetTextarea || mentionStartIndex === -1) return;

                const val = currentTargetTextarea.value;
                const before = val.slice(0, mentionStartIndex);
                const after = val.slice(currentTargetTextarea.selectionStart);
                const insert = '@' + user.username + ' ';

                currentTargetTextarea.value = before + insert + after;
                const newCursor = before.length + insert.length;
                currentTargetTextarea.selectionStart = newCursor;
                currentTargetTextarea.selectionEnd = newCursor;
                currentTargetTextarea.focus();

                closeMentionDropdown();
                updateMentionHighlights(currentTargetTextarea);
            }

            function handleMentionTrigger(target) {
                if (!target || target.tagName !== 'TEXTAREA' || target.name !== 'content') {
                    return;
                }

                const cursorPos = target.selectionStart;
                const textBeforeCursor = target.value.slice(0, cursorPos);

                // Cocokkan apakah ada @ sebelum kursor
                const match = textBeforeCursor.match(/(?:^|\s)@([a-zA-Z0-9_]*)$/);
                if (match) {
                    mentionQuery = match[1];
                    mentionStartIndex = cursorPos - mentionQuery.length - 1;
                    currentTargetTextarea = target;

                    // Gunakan URL relatif agar bekerja di domain manapun (sira.test, localhost, dll)
                    const mentionUrl = "{{ route('api.users.mention', [], false) }}";
                    fetch(`${mentionUrl}?q=${encodeURIComponent(mentionQuery)}`)
                        .then(res => {
                            if (!res.ok) throw new Error('HTTP ' + res.status);
                            return res.json();
                        })
                        .then(data => {
                            if (currentTargetTextarea === target) {
                                currentUsers = data.users || [];
                                highlightedIndex = 0;
                                renderSuggestions();
                            }
                        })
                        .catch(err => {
                            console.error('Mention error:', err);
                            closeMentionDropdown();
                        });
                } else {
                    closeMentionDropdown();
                }
            }

            // Listener Delegasi ke semua textarea komentar (input, click, keyup, scroll)
            document.addEventListener('input', function (e) {
                if (e.target && e.target.tagName === 'TEXTAREA' && e.target.name === 'content') {
                    updateMentionHighlights(e.target);
                    handleMentionTrigger(e.target);
                }
            });

            document.addEventListener('scroll', function (e) {
                if (e.target && e.target.tagName === 'TEXTAREA' && e.target.name === 'content') {
                    syncBackdropScroll(e.target);
                }
            }, true);

            document.addEventListener('click', function (e) {
                if (e.target && e.target.tagName === 'TEXTAREA' && e.target.name === 'content') {
                    handleMentionTrigger(e.target);
                } else if (!dropdown.contains(e.target)) {
                    closeMentionDropdown();
                }
            });

            document.addEventListener('keyup', function (e) {
                if (['ArrowLeft', 'ArrowRight', 'Home', 'End', 'Backspace'].includes(e.key)) {
                    handleMentionTrigger(e.target);
                }
            });

            document.addEventListener('keydown', function (e) {
                if (dropdown.classList.contains('hidden') || !currentUsers || currentUsers.length === 0) {
                    return;
                }

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    highlightedIndex = (highlightedIndex + 1) % currentUsers.length;
                    updateHighlightStyles();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    highlightedIndex = (highlightedIndex - 1 + currentUsers.length) % currentUsers.length;
                    updateHighlightStyles();
                } else if (e.key === 'Enter' || e.key === 'Tab') {
                    if (currentUsers[highlightedIndex]) {
                        e.preventDefault();
                        selectUser(currentUsers[highlightedIndex]);
                    }
                } else if (e.key === 'Escape') {
                    closeMentionDropdown();
                }
            });

            // Reposisi saat scroll atau resize jendela
            window.addEventListener('resize', positionDropdown);
            window.addEventListener('scroll', positionDropdown, true);
        })();

        // Inisialisasi KaTeX & Markdown formatting pada seluruh komentar yang ada
        function initCommentFormatting() {
            formatAllComments();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCommentFormatting);
        } else {
            initCommentFormatting();
        }
        window.addEventListener('load', initCommentFormatting);

        // Polling berkala jika bundle app.js Vite butuh beberapa milidetik untuk parse katex/marked
        let initAttempts = 0;
        const initInterval = setInterval(() => {
            initAttempts++;
            if (window.katex || initAttempts > 25) {
                clearInterval(initInterval);
                initCommentFormatting();
                scrollToTargetComment();
            }
        }, 120);

        // -------------------------------------------------------------
        // Auto-scroll & Efek Highlight ke komentar target saat tautan notifikasi dibuka (#comment-xxx)
        // -------------------------------------------------------------
        function scrollToTargetComment() {
            if (window.location.hash && window.location.hash.startsWith('#comment-')) {
                const targetEl = document.querySelector(window.location.hash);
                if (targetEl) {
                    // Buka container balasan (parent replies) jika komentar berada di dalam percakapan bertingkat
                    let parentReplyContainer = targetEl.closest('[id^="replies-container-"]');
                    while (parentReplyContainer) {
                        parentReplyContainer.classList.remove('hidden');
                        parentReplyContainer = parentReplyContainer.parentElement?.closest('[id^="replies-container-"]');
                    }

                    // Scroll dengan posisi nyaman di tengah layar (tidak tertutup header sticky)
                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    // Beri efek highlight border/ring lembut dan latar bercahaya agar pengguna langsung fokus ke komentar
                    const card = targetEl.querySelector('div:first-child');
                    if (card) {
                        card.classList.add('ring-2', 'ring-emerald-500', 'bg-emerald-50/70', 'dark:bg-emerald-950/40', 'shadow-lg', 'scale-[1.01]', 'transition-all', 'duration-500');
                        setTimeout(() => {
                            card.classList.remove('scale-[1.01]');
                            setTimeout(() => {
                                card.classList.remove('ring-2', 'ring-emerald-500', 'bg-emerald-50/70', 'dark:bg-emerald-950/40', 'shadow-lg');
                            }, 3500);
                        }, 500);
                    }
                }
            }
        }

        // Jalankan auto-scroll & highlight komentar saat DOM siap dan saat URL hash berganti
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(scrollToTargetComment, 150);
        });
        window.addEventListener('load', () => {
            setTimeout(scrollToTargetComment, 250);
        });
        window.addEventListener('hashchange', scrollToTargetComment);

        // -------------------------------------------------------------
        // OpenGraph Dynamic HTML5 Canvas Generator & Exporter
        // -------------------------------------------------------------
        function openOgCanvasModal() {
            const modal = document.getElementById('ogCanvasModal');
            if (modal) {
                modal.classList.remove('hidden');
                renderOgCanvas();
            }
        }

        function closeOgCanvasModal() {
            const modal = document.getElementById('ogCanvasModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeOgCanvasModal();
            }
        });

        function renderOgCanvas() {
            const canvas = document.getElementById('ogCardCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const overlay = document.getElementById('canvasLoadingOverlay');
            if (overlay) overlay.style.display = 'flex';

            const width = 1200;
            const height = 630;
            canvas.width = width;
            canvas.height = height;

            const reportTitle = @json($report->title);
            const reportDesc = @json(preg_replace('/\s+/', ' ', strip_tags($report->description)));
            const reportTier = @json(strtoupper($report->tier_label));
            const reportCategory = @json(strtoupper($report->category_label));
            const reportStatus = @json(strtoupper($report->status_label));
            const reportLocation = @json($report->location_short);
            const reportReporter = @json('@' . ($report->user->username ?? 'anon'));
            const reportVotes = @json($report->vote_score);
            const reportComments = @json($report->comments_count);
            const reportDate = @json($report->created_at->translatedFormat('d M Y'));
            const reportImageSrc = @json($report->image_base64);
            const reportLat = @json(number_format($report->latitude ?? 0, 4));
            const reportLng = @json(number_format($report->longitude ?? 0, 4));
            const rawStatus = @json($report->status);
            const rawTier = @json($report->rank_tier);

            const colors = {
                bg: '#0E0E0E',
                cardBg: '#141414',
                panelBg: '#181818',
                border: '#262626',
                innerBorder: '#303030',
                textWhite: '#EDEDEC',
                textMuted: '#969694',
                textDim: '#6E6E6C',
                emerald: '#10B981',
                rose: '#E11D48',
                amber: '#F59E0B',
                teal: '#0D9488',
                slate: '#64748B',
                sky: '#38BDF8'
            };

            const statusColor = rawStatus === 'resolved' ? colors.emerald :
                (rawStatus === 'in_progress' ? colors.amber :
                    (rawStatus === 'archived' ? colors.slate : colors.sky));

            const statusBg = rawStatus === 'resolved' ? '#12261A' :
                (rawStatus === 'in_progress' ? '#2D1E0A' :
                    (rawStatus === 'archived' ? '#1E242C' : '#0E2032'));

            const tierColor = rawTier === 'critical' ? colors.rose :
                (rawTier === 'urgent' ? colors.amber :
                    (rawTier === 'trending' ? colors.teal : colors.emerald));

            // Background
            ctx.fillStyle = colors.bg;
            ctx.fillRect(0, 0, width, height);

            // Grid pattern
            ctx.strokeStyle = '#181818';
            ctx.lineWidth = 1;
            for (let x = 40; x < width; x += 60) {
                ctx.beginPath();
                ctx.moveTo(x, 0);
                ctx.lineTo(x, height);
                ctx.stroke();
            }
            for (let y = 40; y < height; y += 60) {
                ctx.beginPath();
                ctx.moveTo(0, y);
                ctx.lineTo(width, y);
                ctx.stroke();
            }

            // Inner Card Box
            ctx.fillStyle = colors.cardBg;
            ctx.fillRect(40, 40, width - 80, height - 80);
            ctx.strokeStyle = colors.border;
            ctx.lineWidth = 2;
            ctx.strokeRect(40, 40, width - 80, height - 80);

            // Top Accent Stripe
            ctx.fillStyle = statusColor;
            ctx.fillRect(40, 40, width - 80, 6);

            // Brand Text
            ctx.fillStyle = colors.emerald;
            ctx.font = '600 16px "Geist Mono", monospace';
            ctx.fillText('SIRA // LAPORAN PUBLIK #' + @json($report->id), 70, 92);

            // Dynamically sized Badges
            let badgeX = 70;
            const badgeY = 114;
            const badgeHeight = 28;

            // Helper to draw pill badge
            const drawPillBadge = (text, textColor, fillBg, strokeColor) => {
                ctx.font = 'bold 11px "Plus Jakarta Sans", sans-serif';
                const textWidth = ctx.measureText(text).width;
                const pillWidth = textWidth + 24;
                ctx.fillStyle = fillBg;
                drawCanvasRoundRect(ctx, badgeX, badgeY, pillWidth, badgeHeight, 6, true, false);
                ctx.strokeStyle = strokeColor;
                ctx.lineWidth = 1;
                drawCanvasRoundRect(ctx, badgeX, badgeY, pillWidth, badgeHeight, 6, false, true);
                ctx.fillStyle = textColor;
                ctx.fillText(text, badgeX + 12, badgeY + 18);
                badgeX += pillWidth + 10;
            };

            // 1. Status badge
            drawPillBadge(reportStatus, statusColor, statusBg, statusColor);

            // 2. Category badge
            drawPillBadge(reportCategory, colors.textWhite, '#1C1C1C', colors.innerBorder);

            // 3. Urgency / Tier badge
            drawPillBadge(reportTier, tierColor, '#1A1A1A', tierColor);

            // Title (wrapped max 2 lines)
            ctx.fillStyle = colors.textWhite;
            ctx.font = '800 24px "Plus Jakarta Sans", sans-serif';
            const titleLines = wrapCanvasText(ctx, reportTitle, 600, 2);
            let curY = 192;
            titleLines.forEach(line => {
                ctx.fillText(line, 70, curY);
                curY += 38;
            });

            // Post Content / Description (wrapped, up to 4 lines)
            ctx.fillStyle = colors.textMuted;
            ctx.font = '400 13px "Plus Jakarta Sans", sans-serif';
            const descLines = wrapCanvasText(ctx, reportDesc, 600, 4);
            curY += 10;
            descLines.forEach(line => {
                ctx.fillText(line, 70, curY);
                curY += 24;
            });

            // Info Bar Bottom
            const barY = 465;
            ctx.strokeStyle = colors.border;
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(70, barY);
            ctx.lineTo(width - 70, barY);
            ctx.stroke();

            ctx.fillStyle = colors.textDim;
            ctx.font = '600 11px "Geist Mono", monospace';
            ctx.fillText('LOKASI KEJADIAN', 70, barY + 28);
            ctx.fillText('DUKUNGAN & RESPON', 350, barY + 28);
            ctx.fillText('PELAPOR', 620, barY + 28);
            ctx.fillText('TANGGAL DIBUAT', 880, barY + 28);

            ctx.fillStyle = colors.textWhite;
            ctx.font = 'bold 13px "Plus Jakarta Sans", sans-serif';
            ctx.fillText(truncateCanvasText(reportLocation, 26), 70, barY + 54);

            ctx.fillStyle = colors.emerald;
            ctx.fillText('+' + reportVotes + ' Suara • ' + reportComments + ' Diskusi', 350, barY + 54);

            ctx.fillStyle = colors.textWhite;
            ctx.fillText(truncateCanvasText(reportReporter, 24), 620, barY + 54);

            ctx.fillStyle = colors.textMuted;
            ctx.fillText(reportDate, 880, barY + 54);

            // Watermark Footer
            ctx.fillStyle = colors.textDim;
            ctx.font = '400 11px "Geist Mono", monospace';
            ctx.fillText((window.location.host || 'sira.test') + ' // Sistem Informasi Ruang Aman — Partisipasi Warga Untuk Perubahan Nyata', 70, height - 55);

            // Right-Side Visual: Real Photo Preview OR Rich GIS Radar / Data Card
            const photoX = 705;
            const photoY = 88;
            const photoW = 425;
            const photoH = 355;

            const finishRender = () => {
                if (overlay) overlay.style.display = 'none';
                const statusEl = document.getElementById('canvasRenderStatus');
                if (statusEl) statusEl.textContent = 'Siap Diunduh / Disalin';
            };

            const drawGisFallbackCard = () => {
                ctx.fillStyle = colors.panelBg;
                drawCanvasRoundRect(ctx, photoX, photoY, photoW, photoH, 8, true, false);
                ctx.strokeStyle = colors.innerBorder;
                ctx.lineWidth = 1;
                drawCanvasRoundRect(ctx, photoX, photoY, photoW, photoH, 8, false, true);

                // Header
                ctx.fillStyle = colors.emerald;
                ctx.font = '600 12px "Geist Mono", monospace';
                ctx.fillText('DATA & VERIFIKASI SISTEM', photoX + 22, photoY + 36);

                ctx.strokeStyle = colors.border;
                ctx.beginPath();
                ctx.moveTo(photoX + 22, photoY + 48);
                ctx.lineTo(photoX + photoW - 22, photoY + 48);
                ctx.stroke();

                // Rows
                ctx.fillStyle = colors.textDim;
                ctx.font = '600 10px "Geist Mono", monospace';
                ctx.fillText('STATUS PENANGANAN', photoX + 22, photoY + 76);

                ctx.fillStyle = statusColor;
                ctx.font = 'bold 14px "Plus Jakarta Sans", sans-serif';
                ctx.fillText(reportStatus, photoX + 22, photoY + 98);

                ctx.fillStyle = colors.textDim;
                ctx.font = '600 10px "Geist Mono", monospace';
                ctx.fillText('WILAYAH & KOORDINAT GIS', photoX + 22, photoY + 132);

                ctx.fillStyle = colors.textWhite;
                ctx.font = 'bold 13px "Plus Jakarta Sans", sans-serif';
                ctx.fillText(truncateCanvasText(reportLocation, 34), photoX + 22, photoY + 154);

                ctx.fillStyle = colors.textMuted;
                ctx.font = '11px "Geist Mono", monospace';
                ctx.fillText('Lat: ' + reportLat + ' | Lng: ' + reportLng, photoX + 22, photoY + 176);

                ctx.fillStyle = colors.textDim;
                ctx.font = '600 10px "Geist Mono", monospace';
                ctx.fillText('DUKUNGAN & TANGGAPAN WARGA', photoX + 22, photoY + 210);

                ctx.fillStyle = colors.emerald;
                ctx.font = 'bold 13px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('+' + reportVotes + ' Suara Warga • ' + reportComments + ' Tanggapan', photoX + 22, photoY + 232);

                ctx.fillStyle = colors.textDim;
                ctx.font = '600 10px "Geist Mono", monospace';
                ctx.fillText('PRIORITAS ALGORITMA WILSON', photoX + 22, photoY + 266);

                ctx.fillStyle = tierColor;
                ctx.font = 'bold 13px "Plus Jakarta Sans", sans-serif';
                ctx.fillText(reportTier + ' (ID #' + @json($report->id) + ')', photoX + 22, photoY + 288);

                ctx.fillStyle = colors.textDim;
                ctx.font = '400 10px "Geist Mono", monospace';
                ctx.fillText('OPENMAP GIS RADAR // TERVERIFIKASI SISTEM', photoX + 22, photoY + 332);

                finishRender();
            };

            // If image is raster (PNG/JPEG), load in Image(), else draw GIS fallback directly
            const isSvg = reportImageSrc && reportImageSrc.includes('svg');
            if (reportImageSrc && !isSvg) {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = function () {
                    ctx.save();
                    drawCanvasRoundRect(ctx, photoX, photoY, photoW, photoH, 8, false, false);
                    ctx.clip();

                    const scale = Math.max(photoW / img.naturalWidth, photoH / img.naturalHeight);
                    const cropW = photoW / scale;
                    const cropH = photoH / scale;
                    const cropX = (img.naturalWidth - cropW) / 2;
                    const cropY = (img.naturalHeight - cropH) / 2;

                    ctx.drawImage(img, cropX, cropY, cropW, cropH, photoX, photoY, photoW, photoH);
                    ctx.restore();

                    // Overlay bottom pill on image
                    ctx.fillStyle = 'rgba(10, 10, 10, 0.75)';
                    ctx.fillRect(photoX, photoY + photoH - 40, photoW, 40);

                    ctx.fillStyle = colors.textWhite;
                    ctx.font = 'bold 11px "Plus Jakarta Sans", sans-serif';
                    ctx.fillText('FOTO BUKTI LAPORAN // ' + reportCategory, photoX + 16, photoY + photoH - 15);

                    ctx.strokeStyle = colors.innerBorder;
                    ctx.lineWidth = 1;
                    drawCanvasRoundRect(ctx, photoX, photoY, photoW, photoH, 8, false, true);

                    finishRender();
                };
                img.onerror = function () {
                    drawGisFallbackCard();
                };
                img.src = reportImageSrc;
            } else {
                drawGisFallbackCard();
            }
        }

        function drawCanvasRoundRect(ctx, x, y, width, height, radius, fill, stroke) {
            if (typeof radius === 'undefined') radius = 6;
            ctx.beginPath();
            ctx.moveTo(x + radius, y);
            ctx.lineTo(x + width - radius, y);
            ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
            ctx.lineTo(x + width, y + height - radius);
            ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
            ctx.lineTo(x + radius, y + height);
            ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
            ctx.lineTo(x, y + radius);
            ctx.quadraticCurveTo(x, y, x + radius, y);
            ctx.closePath();
            if (fill) ctx.fill();
            if (stroke) ctx.stroke();
        }

        function wrapCanvasText(ctx, text, maxWidth, maxLines) {
            if (!text) return [];
            const words = text.split(' ');
            const lines = [];
            let currentLine = '';

            for (let i = 0; i < words.length; i++) {
                const testLine = currentLine ? currentLine + ' ' + words[i] : words[i];
                const metrics = ctx.measureText(testLine);
                if (metrics.width > maxWidth && currentLine) {
                    lines.push(currentLine);
                    currentLine = words[i];
                    if (lines.length === maxLines - 1) {
                        break;
                    }
                } else {
                    currentLine = testLine;
                }
            }
            if (currentLine && lines.length < maxLines) {
                lines.push(currentLine);
            }
            if (lines.length === maxLines && words.length > lines.join(' ').split(' ').length) {
                lines[maxLines - 1] = lines[maxLines - 1].replace(/[\s.,]+$/, '') + '...';
            }
            return lines;
        }

        function truncateCanvasText(text, limit) {
            if (!text) return '';
            return text.length > limit ? text.substring(0, limit) + '...' : text;
        }

        function downloadOgCanvas() {
            const canvas = document.getElementById('ogCardCanvas');
            if (!canvas) return;
            const link = document.createElement('a');
            link.download = 'sira-laporan-' + @json($report->id) + '-opengraph.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        }

        async function copyOgCanvasToClipboard() {
            const canvas = document.getElementById('ogCardCanvas');
            const btnText = document.getElementById('copyImageText');
            if (!canvas) return;

            if (!navigator.clipboard || !window.ClipboardItem) {
                downloadOgCanvas();
                return;
            }

            try {
                canvas.toBlob(async (blob) => {
                    if (!blob) return;
                    await navigator.clipboard.write([
                        new ClipboardItem({ 'image/png': blob })
                    ]);
                    if (btnText) {
                        const orig = btnText.textContent;
                        btnText.textContent = 'Tersalin ke Clipboard!';
                        setTimeout(() => { btnText.textContent = orig; }, 2500);
                    }
                }, 'image/png');
            } catch (err) {
                console.error('Copy canvas to clipboard error:', err);
                downloadOgCanvas();
            }
        }

        function copyReportUrl() {
            const btnText = document.getElementById('copyUrlText');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    if (btnText) {
                        const orig = btnText.textContent;
                        btnText.textContent = 'Tautan Tersalin!';
                        setTimeout(() => { btnText.textContent = orig; }, 2500);
                    }
                });
            }
        }
    </script>
@endpush