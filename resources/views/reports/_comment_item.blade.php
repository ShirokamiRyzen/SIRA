<div class="space-y-3" id="comment-{{ $comment->id }}">
    <div class="flex items-start space-x-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
        <!-- User Initial Avatar -->
        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs shrink-0 uppercase">
            {{ substr($comment->user->username ?? 'U', 0, 1) }}
        </div>

        <div class="flex-1 min-w-0">
            <!-- Header: Username & Date -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-bold text-slate-900">@<span>{{ $comment->user->username ?? 'anon' }}</span></span>
                    <span class="text-[11px] text-slate-400">&bull; {{ $comment->created_at->diffForHumans() }}</span>
                </div>

                @auth
                    @if (Auth::id() === $comment->user_id)
                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Hapus komentar ini?')" class="inline">
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
            <p class="text-xs text-slate-700 mt-1 leading-relaxed whitespace-pre-line">{{ $comment->content }}</p>

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
                    <form action="{{ route('comments.store', $comment->report_id) }}" method="POST" class="space-y-2">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                        <textarea name="content" rows="2" required placeholder="Tulis balasan untuk @<span>{{ $comment->user->username }}</span>..."
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                        <div class="flex items-center justify-end space-x-2">
                            <button type="button" onclick="toggleReplyForm({{ $comment->id }})" class="px-2.5 py-1 text-xs text-slate-500 hover:bg-slate-200 rounded-lg">
                                Batal
                            </button>
                            <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm">
                                Kirim Balasan
                            </button>
                        </div>
                    </form>
                </div>
            @endauth
        </div>
    </div>

    <!-- Recursive Nested Replies -->
    @if ($comment->replies && $comment->replies->count() > 0)
        <div class="pl-6 border-l-2 border-slate-200 space-y-3 mt-3">
            @foreach ($comment->replies as $reply)
                @include('reports._comment_item', ['comment' => $reply])
            @endforeach
        </div>
    @endif
</div>
