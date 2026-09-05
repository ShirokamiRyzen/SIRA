<div class="space-y-3" id="comment-{{ $comment->id }}">
    <div class="flex items-start space-x-3 p-3.5 rounded-2xl {{ ($comment->user && strtolower($comment->user->username) === 'sira') ? 'bg-gradient-to-r from-indigo-50/80 to-purple-50/80 border border-indigo-200/90 shadow-sm' : 'bg-slate-50 border border-slate-200/80' }}">
        <!-- User Initial Avatar / AI Robot Avatar -->
        @if ($comment->user && strtolower($comment->user->username) === 'sira')
            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-600 text-white font-bold flex items-center justify-center text-sm shrink-0 shadow-sm ring-2 ring-indigo-200">
                🤖
            </div>
        @else
            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs shrink-0 uppercase">
                {{ substr($comment->user->username ?? 'U', 0, 1) }}
            </div>
        @endif

        <div class="flex-1 min-w-0">
            <!-- Header: Username & Date -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                    <span class="text-xs font-bold text-slate-900">@<span>{{ $comment->user->username ?? 'anon' }}</span></span>
                    @if ($comment->user && strtolower($comment->user->username) === 'sira')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-600 text-white shadow-xs">
                            SIRA AI ASSISTANT
                        </span>
                    @endif
                    <span class="text-[11px] text-slate-400">&bull; {{ $comment->created_at->diffForHumans() }}</span>
                </div>

                @auth
                    @if (Auth::id() === $comment->user_id)
                        <form action="{{ route('comments.destroy', $comment, false) }}" method="POST" onsubmit="deleteCommentAjax(event, this, {{ $comment->id }})" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-[11px] text-slate-400 hover:text-rose-600 transition" title="Hapus komentar">
                                Hapus
                            </button>
                        </form>
                    @endif
                @endauth
            </div>

            <!-- Content -->
            @php
                $formattedBladeContent = preg_replace_callback('/(^|[^a-zA-Z0-9_])@([a-zA-Z0-9_]+)/', function($m) {
                    $isAi = strtolower($m[2]) === 'sira';
                    $cls = $isAi 
                        ? 'font-bold text-indigo-700 bg-indigo-50 border border-indigo-200/80 px-1.5 py-0.5 rounded-md'
                        : 'font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-1.5 py-0.5 rounded-md';
                    return $m[1] . '<span class="inline-flex items-center ' . $cls . '">@' . $m[2] . '</span>';
                }, e($comment->content));
            @endphp
            <div class="comment-body text-xs {{ ($comment->user && strtolower($comment->user->username) === 'sira') ? 'text-indigo-950 font-medium' : 'text-slate-700' }} mt-1.5 leading-relaxed" data-raw-content="{{ e($comment->content) }}">{!! $formattedBladeContent !!}</div>

            <!-- Reply Button -->
            @auth
                <div class="mt-2">
                    <button type="button" onclick="toggleReplyForm({{ $comment->id }})" class="text-[11px] font-semibold text-emerald-700 hover:text-emerald-800 hover:underline inline-flex items-center space-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        <span>Balas</span>
                    </button>
                </div>

                <!-- Hidden Inline Reply Form -->
                <div id="reply-form-{{ $comment->id }}" class="hidden mt-3 pt-3 border-t border-slate-200">
                    <form action="{{ route('comments.store', $comment->report_id, false) }}" method="POST" class="space-y-2" onsubmit="submitCommentAjax(event, this, {{ $comment->id }})">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                        <textarea name="content" rows="2" required placeholder="Tulis balasan untuk @<span>{{ $comment->user->username }}</span> (Tag @Sira untuk bantuan AI)..."
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>

                        <!-- Pratinjau LaTeX jika terdapat formula matematika -->
                        <div class="latex-preview hidden px-3 py-2 bg-slate-100/80 border border-slate-200 rounded-xl text-xs text-slate-800 space-y-1">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center space-x-1">
                                <span>📐 Pratinjau LaTeX:</span>
                            </div>
                            <div class="latex-preview-content font-sans overflow-x-auto"></div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-slate-400 italic">💡 Tag @Sira untuk memanggil bot AI</span>
                            <div class="flex items-center space-x-2">
                                <button type="button" onclick="toggleReplyForm({{ $comment->id }})" class="px-2.5 py-1 text-xs text-slate-500 hover:bg-slate-200 rounded-lg">
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
    <div id="replies-container-{{ $comment->id }}" class="pl-6 border-l-2 border-slate-200 space-y-3 mt-3 {{ ($comment->replies && $comment->replies->count() > 0) ? '' : 'hidden' }}">
        @if ($comment->replies && $comment->replies->count() > 0)
            @foreach ($comment->replies as $reply)
                @include('reports._comment_item', ['comment' => $reply])
            @endforeach
        @endif
    </div>
</div>
