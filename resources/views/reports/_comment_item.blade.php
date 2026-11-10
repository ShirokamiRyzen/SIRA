<div class="space-y-3 scroll-mt-28 transition-all duration-300 relative" id="comment-{{ $comment->id }}">
    <div class="flex items-start gap-3 p-3.5 rounded-xl border {{ ($comment->user && strtolower($comment->user->username) === 'sira') ? 'bg-emerald-50/70 border-emerald-200 dark:bg-emerald-950/30 dark:border-emerald-900/60' : 'bg-sand-25 border-sand-200 dark:bg-sand-950 dark:border-sand-900' }}">
        <!-- User Initial Avatar / AI Robot Avatar -->
        @if ($comment->user && strtolower($comment->user->username) === 'sira')
            <img src="{{ asset('android-chrome-192x192.png') }}" alt="SIRA AI" class="w-8 h-8 rounded-full object-cover shrink-0 shadow-sm ring-2 ring-emerald-200 dark:ring-emerald-900">
        @else
            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 font-bold flex items-center justify-center text-xs shrink-0 uppercase">
                {{ substr($comment->user->username ?? 'U', 0, 1) }}
            </div>
        @endif

        <div class="flex-1 min-w-0">
            <!-- Header: Username & Date -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-1.5 flex-wrap gap-y-1">
                    <span class="text-sm font-semibold text-sand-975 dark:text-sand-100">@<span>{{ $comment->user->username ?? 'anon' }}</span></span>
                    @if ($comment->user)
                        <x-verified-badge :user="$comment->user" size="xs" />
                    @endif
                    @if ($comment->user && strtolower($comment->user->username) === 'sira')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-600 text-white">
                            Asisten AI
                        </span>
                    @endif
                    <span class="text-xs text-sand-500 dark:text-sand-600">&middot; {{ $comment->created_at->diffForHumans() }}</span>
                </div>

                @auth
                    @if (Auth::id() === $comment->user_id || Auth::user()?->isAdmin())
                        <form action="{{ route('comments.destroy', $comment, false) }}" method="POST" onsubmit="deleteCommentAjax(event, this, {{ $comment->id }})" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-sand-500 hover:text-danger dark:text-sand-600 dark:hover:text-danger-soft transition flex items-center space-x-1" title="Hapus komentar">
                                <span>Hapus</span>
                            </button>
                        </form>
                    @endif
                @endauth
            </div>

            <!-- Content -->
            @php
                $formattedBladeContent = preg_replace_callback('/(^|[^a-zA-Z0-9_])@([a-zA-Z0-9_]+)/', function($m) {
                    $u = $m[2];
                    static $userMentionCache = [];

                    if (strtolower($u) === 'sira') {
                        $badgeType = null;
                    } else {
                        if (! array_key_exists($u, $userMentionCache)) {
                            $userMentionCache[$u] = \App\Models\User::query()
                                ->select(['id', 'username', 'is_admin', 'is_verified'])
                                ->where('username', $u)
                                ->first();
                        }
                        $targetUser = $userMentionCache[$u];
                        $badgeType = $targetUser ? $targetUser->badgeType() : null;
                    }

                    $badgeSvg = '';
                    if ($badgeType === 'admin') {
                        $badgeSvg = '<svg class="w-3 h-3 text-amber-500 fill-current inline-block shrink-0 ml-0.5 align-baseline" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.516 2.17a.75.75 0 0 0-1.032 0 11.209 11.209 0 0 1-7.877 3.08.75.75 0 0 0-.722.515A12.74 12.74 0 0 0 2.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.749.749 0 0 0 .374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.39-.223-2.73-.635-3.985a.75.75 0 0 0-.722-.516l-.143.001c-2.996 0-5.717-1.17-7.734-3.08Zm3.094 8.016a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg>';
                    } elseif ($badgeType === 'verified') {
                        $badgeSvg = '<svg class="w-3 h-3 text-sky-500 fill-current inline-block shrink-0 ml-0.5 align-baseline" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg>';
                    }

                    return $m[1] . '<span class="font-semibold text-emerald-700 dark:text-emerald-400 hover:underline inline-flex items-center gap-0.5">@' . $u . $badgeSvg . '</span>';
                }, e($comment->content));
            @endphp
            <div class="comment-body text-sm text-sand-800 dark:text-sand-300 mt-1 leading-relaxed" data-raw-content="{{ e($comment->content) }}">{!! $formattedBladeContent !!}</div>

            <!-- Reply Button -->
            @auth
                <div class="mt-2">
                    <button type="button" onclick="toggleReplyForm({{ $comment->id }}, '{{ $comment->user->username ?? '' }}')" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300 hover:underline inline-flex items-center space-x-1 cursor-pointer">
                        <flux:icon name="arrow-uturn-left" class="w-3 h-3" />
                        <span>Balas</span>
                    </button>
                </div>

                <!-- Hidden Inline Reply Form -->
                <div id="reply-form-{{ $comment->id }}" class="hidden mt-3 pt-3 border-t border-slate-200 dark:border-sand-850">
                    <form action="{{ route('comments.store', $comment->report_id, false) }}" method="POST" class="space-y-2" onsubmit="submitCommentAjax(event, this, {{ $comment->id }})">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                        <div class="mention-highlighter-wrapper mention-highlighter-sm relative w-full rounded-xl border border-slate-300 dark:border-sand-850 bg-white dark:bg-sand-975 focus-within:ring-2 focus-within:ring-emerald-500 focus-within:border-emerald-500 overflow-hidden transition">
                            <div class="mention-backdrop absolute inset-0 pointer-events-none px-3 py-2 text-xs font-sans leading-relaxed text-transparent overflow-hidden select-none whitespace-pre-wrap break-words" aria-hidden="true"></div>
                            <textarea name="content" rows="2" required placeholder="Tulis balasan untuk @<span>{{ $comment->user->username ?? 'pengguna' }}</span>..."
                                data-target-user="{{ $comment->user->username ?? '' }}"
                                class="mention-input relative z-10 w-full px-3 py-2 bg-transparent text-slate-900 dark:text-sand-100 placeholder-slate-400 dark:placeholder-sand-600 text-xs font-sans leading-relaxed focus:outline-none resize-y block border-0 ring-0 focus:ring-0">@if($comment->user){{ '@' . $comment->user->username . ' ' }}@endif</textarea>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="flex items-center space-x-1 text-[11px] text-sand-500 dark:text-sand-600">
                                <flux:icon name="sparkles" class="w-3 h-3 text-emerald-600 dark:text-emerald-400 shrink-0" />
                                <span>Tag @Sira untuk asisten AI</span>
                            </span>
                            <div class="flex items-center space-x-2">
                                <button type="button" onclick="toggleReplyForm({{ $comment->id }})" class="px-2.5 py-1 text-xs text-slate-500 hover:bg-slate-200 dark:text-sand-500 dark:hover:bg-sand-900 rounded-lg">
                                    Batal
                                </button>
                                <button type="submit" class="submit-btn btn-primary px-3 py-1.5 text-xs font-semibold rounded-lg flex items-center space-x-1">
                                    <span>Kirim Balasan</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endauth
        </div>
    </div>

    <!-- Recursive Nested Replies Container -->
    <div id="replies-container-{{ $comment->id }}" class="comment-replies-container pl-6 sm:pl-8 space-y-3 mt-3 {{ ($comment->replies && $comment->replies->count() > 0) ? '' : 'hidden' }}">
        @if ($comment->replies && $comment->replies->count() > 0)
            @foreach ($comment->replies as $reply)
                @include('reports._comment_item', ['comment' => $reply])
            @endforeach
        @endif
    </div>
</div>
