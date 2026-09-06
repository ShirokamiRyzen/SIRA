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
                <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                    <span class="text-xs font-bold text-slate-900 dark:text-[#EDEDEC]">@<span>{{ $comment->user->username ?? 'anon' }}</span></span>
                    @if ($comment->user && strtolower($comment->user->username) === 'sira')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-600 text-white shadow-xs">
                            SIRA AI ASSISTANT
                        </span>
                    @endif
                    <span class="text-[11px] text-slate-400 dark:text-[#787774]">&bull; {{ $comment->created_at->diffForHumans() }}</span>
                </div>

                @auth
                    @if (Auth::id() === $comment->user_id)
                        <form action="{{ route('comments.destroy', $comment, false) }}" method="POST" onsubmit="deleteCommentAjax(event, this, {{ $comment->id }})" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-[11px] text-slate-400 hover:text-rose-600 dark:text-[#787774] dark:hover:text-rose-400 transition" title="Hapus komentar">
                                Hapus
                            </button>
                        </form>
                    @endif
                @endauth
            </div>

            <!-- Content -->
            @php
                $currentAuthUsername = Auth::user()?->username ?? '';
                $formattedBladeContent = preg_replace_callback('/(^|[^a-zA-Z0-9_])@([a-zA-Z0-9_]+)/', function($m) use ($currentAuthUsername) {
                    $u = $m[2];
                    $isAi = strtolower($u) === 'sira';
                    $isMe = $currentAuthUsername && strcasecmp($u, $currentAuthUsername) === 0;

                    if ($isAi) {
                        $cls = 'font-bold text-indigo-700 dark:text-indigo-200 bg-indigo-100/90 dark:bg-indigo-900/60 border border-indigo-300/80 dark:border-indigo-700/80 px-2 py-0.5 rounded-lg shadow-xs ring-1 ring-indigo-400/30';
                        $icon = '<svg class="w-3 h-3 text-indigo-600 dark:text-indigo-300 inline mr-0.5" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a2 2 0 0 1 2 2v1h1a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1V3a2 2 0 0 1 2-2z"/></svg>';
                    } elseif ($isMe) {
                        $cls = 'font-bold text-amber-900 dark:text-amber-100 bg-amber-200/90 dark:bg-amber-900/70 border border-amber-400 dark:border-amber-600 px-2 py-0.5 rounded-lg shadow-xs ring-2 ring-amber-400/60';
                        $icon = '<span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block mr-1"></span>';
                    } else {
                        $cls = 'font-bold text-emerald-700 dark:text-emerald-200 bg-emerald-100/90 dark:bg-emerald-900/60 border border-emerald-300/80 dark:border-emerald-700/80 px-2 py-0.5 rounded-lg shadow-xs ring-1 ring-emerald-400/30';
                        $icon = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block mr-1"></span>';
                    }

                    return $m[1] . '<span class="inline-flex items-center mx-0.5 ' . $cls . '">' . $icon . '@' . $u . '</span>';
                }, e($comment->content));
            @endphp
            <div class="comment-body text-xs {{ ($comment->user && strtolower($comment->user->username) === 'sira') ? 'text-indigo-950 dark:text-indigo-200 font-medium' : 'text-slate-700 dark:text-[#CCCCCC]' }} mt-1.5 leading-relaxed" data-raw-content="{{ e($comment->content) }}">{!! $formattedBladeContent !!}</div>

            <!-- Reply Button -->
            @auth
                <div class="mt-2">
                    <button type="button" onclick="toggleReplyForm({{ $comment->id }})" class="text-[11px] font-semibold text-emerald-700 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300 hover:underline inline-flex items-center space-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        <span>Balas</span>
                    </button>
                </div>

                <!-- Hidden Inline Reply Form -->
                <div id="reply-form-{{ $comment->id }}" class="hidden mt-3 pt-3 border-t border-slate-200 dark:border-[#282828]">
                    <form action="{{ route('comments.store', $comment->report_id, false) }}" method="POST" class="space-y-2" onsubmit="submitCommentAjax(event, this, {{ $comment->id }})">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                        <textarea name="content" rows="2" required placeholder="Tulis balasan untuk @<span>{{ $comment->user->username }}</span> (Tag @Sira untuk bantuan AI)..."
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-[#282828] bg-white dark:bg-[#121212] text-slate-900 dark:text-[#EDEDEC] placeholder-slate-400 dark:placeholder-[#666666] focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>

                        <!-- Pratinjau LaTeX jika terdapat formula matematika -->
                        <div class="latex-preview hidden px-3 py-2 bg-slate-100/80 dark:bg-[#1A1A1A] border border-slate-200 dark:border-[#282828] rounded-xl text-xs text-slate-800 dark:text-[#CCCCCC] space-y-1">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center space-x-1">
                                <flux:icon name="calculator" class="w-3 h-3 text-slate-400" />
                                <span>Pratinjau LaTeX:</span>
                            </div>
                            <div class="latex-preview-content font-sans overflow-x-auto"></div>
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
