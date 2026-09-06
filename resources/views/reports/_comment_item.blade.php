<div class="space-y-3 scroll-mt-28 transition-all duration-300" id="comment-{{ $comment->id }}">
    <div class="flex items-start space-x-3 p-3.5 rounded-2xl {{ ($comment->user && strtolower($comment->user->username) === 'sira') ? 'bg-gradient-to-r from-indigo-50/80 to-purple-50/80 dark:from-indigo-950/40 dark:to-purple-950/40 border border-indigo-200/90 dark:border-indigo-800/60 shadow-sm' : 'bg-slate-50 dark:bg-[#181818] border border-slate-200/80 dark:border-[#262626]' }}">
        <!-- User Initial Avatar / AI Robot Avatar -->
        @if ($comment->user && strtolower($comment->user->username) === 'sira')
            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-600 text-white font-bold flex items-center justify-center text-sm shrink-0 shadow-sm ring-2 ring-indigo-200 dark:ring-indigo-900/60">
                <flux:icon name="cpu-chip" class="w-4 h-4 text-white" />
            </div>
        @else
            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 font-bold flex items-center justify-center text-xs shrink-0 uppercase">
                {{ substr($comment->user->username ?? 'U', 0, 1) }}
            </div>
        @endif

        <div class="flex-1 min-w-0">
            <!-- Header: Username & Date -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-1.5 flex-wrap gap-y-1">
                    <span class="text-xs font-bold text-slate-900 dark:text-[#EDEDEC]">@<span>{{ $comment->user->username ?? 'anon' }}</span></span>
                    @if ($comment->user)
                        <x-verified-badge :user="$comment->user" size="xs" />
                    @endif
                    @if ($comment->user && strtolower($comment->user->username) === 'sira')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-600 text-white shadow-xs">
                            SIRA AI ASSISTANT
                        </span>
                    @endif
                    <span class="text-[11px] text-slate-400 dark:text-[#787774]">&bull; {{ $comment->created_at->diffForHumans() }}</span>
                </div>

                @auth
                    @if (Auth::id() === $comment->user_id || Auth::user()?->isAdmin())
                        <form action="{{ route('comments.destroy', $comment, false) }}" method="POST" onsubmit="deleteCommentAjax(event, this, {{ $comment->id }})" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-[11px] text-slate-400 hover:text-rose-600 dark:text-[#787774] dark:hover:text-rose-400 transition flex items-center space-x-1" title="Hapus komentar">
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
                    $targetUser = \App\Models\User::where('username', $u)->first();
                    $badgeType = strtolower($u) === 'sira' ? null : ($targetUser ? $targetUser->badgeType() : null);

                    $badgeSvg = '';
                    if ($badgeType === 'admin') {
                        $badgeSvg = '<svg class="w-3 h-3 text-amber-500 fill-current inline-block shrink-0 ml-0.5 align-baseline" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.516 2.17a.75.75 0 0 0-1.032 0 11.209 11.209 0 0 1-7.877 3.08.75.75 0 0 0-.722.515A12.74 12.74 0 0 0 2.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.749.749 0 0 0 .374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.39-.223-2.73-.635-3.985a.75.75 0 0 0-.722-.516l-.143.001c-2.996 0-5.717-1.17-7.734-3.08Zm3.094 8.016a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg>';
                    } elseif ($badgeType === 'verified') {
                        $badgeSvg = '<svg class="w-3 h-3 text-sky-500 fill-current inline-block shrink-0 ml-0.5 align-baseline" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg>';
                    }

                    return $m[1] . '<span class="font-bold text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-0.5">@' . $u . $badgeSvg . '</span>';
                }, e($comment->content));
            @endphp
            <div class="comment-body text-xs {{ ($comment->user && strtolower($comment->user->username) === 'sira') ? 'text-indigo-950 dark:text-indigo-200 font-medium' : 'text-slate-700 dark:text-[#CCCCCC]' }} mt-1.5 leading-relaxed" data-raw-content="{{ e($comment->content) }}">{!! $formattedBladeContent !!}</div>

            <!-- Reply Button -->
            @auth
                <div class="mt-2">
                    <button type="button" onclick="toggleReplyForm({{ $comment->id }}, '{{ $comment->user->username ?? '' }}')" class="text-[11px] font-semibold text-emerald-700 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300 hover:underline inline-flex items-center space-x-1 cursor-pointer">
                        <flux:icon name="arrow-uturn-left" class="w-3 h-3" />
                        <span>Balas</span>
                    </button>
                </div>

                <!-- Hidden Inline Reply Form -->
                <div id="reply-form-{{ $comment->id }}" class="hidden mt-3 pt-3 border-t border-slate-200 dark:border-[#282828]">
                    <form action="{{ route('comments.store', $comment->report_id, false) }}" method="POST" class="space-y-2" onsubmit="submitCommentAjax(event, this, {{ $comment->id }})">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                        <div class="mention-highlighter-wrapper mention-highlighter-sm relative w-full rounded-xl border border-slate-300 dark:border-[#282828] bg-white dark:bg-[#121212] focus-within:ring-2 focus-within:ring-emerald-500 focus-within:border-emerald-500 overflow-hidden transition">
                            <div class="mention-backdrop absolute inset-0 pointer-events-none px-3 py-2 text-xs font-sans leading-relaxed text-transparent overflow-hidden select-none whitespace-pre-wrap break-words" aria-hidden="true"></div>
                            <textarea name="content" rows="2" required placeholder="Tulis balasan untuk @<span>{{ $comment->user->username ?? 'pengguna' }}</span>..."
                                data-target-user="{{ $comment->user->username ?? '' }}"
                                class="mention-input relative z-10 w-full px-3 py-2 bg-transparent text-slate-900 dark:text-[#EDEDEC] placeholder-slate-400 dark:placeholder-[#666666] text-xs font-sans leading-relaxed focus:outline-none resize-y block border-0 ring-0 focus:ring-0">@if($comment->user){{ '@' . $comment->user->username . ' ' }}@endif</textarea>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="flex items-center space-x-1 text-[10px] text-slate-400 dark:text-[#787774] italic">
                                <flux:icon name="sparkles" class="w-3 h-3 text-amber-500 shrink-0" />
                                <span>Tag @Sira untuk asisten AI</span>
                            </span>
                            <div class="flex items-center space-x-2">
                                <button type="button" onclick="toggleReplyForm({{ $comment->id }})" class="px-2.5 py-1 text-xs text-slate-500 hover:bg-slate-200 dark:text-[#888888] dark:hover:bg-[#252525] rounded-lg">
                                    Batal
                                </button>
                                <button type="submit" class="submit-btn px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm flex items-center space-x-1">
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
    <div id="replies-container-{{ $comment->id }}" class="pl-6 border-l-2 border-slate-200 dark:border-[#262626] space-y-3 mt-3 {{ ($comment->replies && $comment->replies->count() > 0) ? '' : 'hidden' }}">
        @if ($comment->replies && $comment->replies->count() > 0)
            @foreach ($comment->replies as $reply)
                @include('reports._comment_item', ['comment' => $reply])
            @endforeach
        @endif
    </div>
</div>
