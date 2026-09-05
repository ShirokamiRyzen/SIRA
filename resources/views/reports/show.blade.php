@extends('layouts.app')

@section('title', $report->title . ' - SIRA')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <!-- Breadcrumb -->
    <div>
        <a href="{{ route('reports.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-emerald-700 transition space-x-1">
            <span>&larr;</span>
            <span>Kembali ke Semua Laporan</span>
        </a>
    </div>

    <!-- Main Card Header & Grid -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
            <!-- Kolom Kiri: Foto Bukti Base64 -->
            <div class="lg:col-span-7 bg-slate-950 flex items-center justify-center p-2 relative min-h-[350px]">
                <img src="{{ $report->image_base64 }}" alt="{{ $report->title }}" class="max-h-[500px] w-auto max-w-full object-contain rounded-xl">
                
                <!-- Rank Tier Badge Overlay -->
                <div class="absolute top-4 left-4" id="tierBadgeContainer">
                    @if ($report->rank_tier === 'critical')
                        <span class="inline-flex items-center space-x-1.5 px-3.5 py-1 rounded-full text-xs font-black bg-rose-600 text-white shadow-lg">
                            <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                            <span>CRITICAL TIER</span>
                        </span>
                    @elseif ($report->rank_tier === 'urgent')
                        <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold bg-amber-500 text-white shadow-md">
                            ⚠️ URGENT TIER
                        </span>
                    @elseif ($report->rank_tier === 'trending')
                        <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold bg-teal-600 text-white shadow-md">
                            🔥 TRENDING TIER
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-800/80 backdrop-blur-md text-white">
                            NORMAL TIER
                        </span>
                    @endif
                </div>
            </div>

            <!-- Kolom Kanan: Detail & Aksi Voting -->
            <div class="lg:col-span-5 p-6 sm:p-8 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <!-- Status & Waktu & Aksi Khusus Pembuat Laporan -->
                    <div class="flex items-center justify-between text-xs flex-wrap gap-2 pb-1">
                        <div class="flex items-center space-x-2">
                            <span id="reportStatusBadge" class="px-2.5 py-1 rounded-full font-bold uppercase tracking-wider transition duration-200 {{ $report->status === 'resolved' ? 'bg-emerald-600 text-white shadow-xs' : ($report->status === 'in_progress' ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-emerald-50 text-emerald-700 border border-emerald-200') }}">
                                Status: {{ str_replace('_', ' ', $report->status) }}
                            </span>

                            @auth
                                @if (Auth::id() === $report->user_id)
                                    <div id="creatorStatusActions" class="inline-flex items-center">
                                        @if ($report->status === 'resolved')
                                            <button type="button" onclick="updateReportStatus('active')" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition flex items-center space-x-1" title="Buka kembali laporan ini">
                                                <span>↩</span>
                                                <span>Buka Kembali</span>
                                            </button>
                                        @else
                                            <button type="button" onclick="updateReportStatus('resolved')" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition flex items-center space-x-1 shadow-xs" title="Tandai masalah telah terselesaikan">
                                                <span>✓</span>
                                                <span>Tandai Selesai (Resolved)</span>
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            @endauth
                        </div>
                        <span class="text-slate-400">{{ $report->created_at->translatedFormat('d M Y, H:i') }}</span>
                    </div>

                    <!-- Judul Laporan -->
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-tight">
                        {{ $report->title }}
                    </h1>

                    <!-- Pelapor -->
                    <div class="flex items-center space-x-2 text-xs text-slate-500 pb-2 border-b border-slate-100">
                        <span>Dilaporkan oleh</span>
                        <span class="font-bold text-slate-800">@<span>{{ $report->user->username ?? 'anon' }}</span></span>
                        @auth
                            @if (Auth::id() === $report->user_id)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    Laporan Anda
                                </span>
                            @endif
                        @endauth
                    </div>

                    <!-- Deskripsi Lengkap -->
                    <div class="text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                        {{ $report->description }}
                    </div>

                    <!-- Lokasi Administratif -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                        <div class="font-bold text-slate-800 flex items-center space-x-1">
                            <span>📍</span>
                            <span>{{ $report->district ? $report->district . ', ' : '' }}{{ $report->city ?? 'Lokasi Terdaftar' }}</span>
                        </div>
                        <p class="text-slate-500 text-[11px] leading-relaxed">
                            {{ $report->formatted_address ?? 'Koordinat: ' . $report->latitude . ', ' . $report->longitude }}
                        </p>
                    </div>
                </div>

                <!-- Box Voting (Like & Dislike) Interaktif -->
                <div class="p-4 rounded-2xl bg-slate-900 text-white space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-slate-400 font-medium">Skor Dukungan Warga</div>
                            <div class="text-2xl font-extrabold text-emerald-400" id="voteScoreDisplay">
                                {{ $report->vote_score }} <span class="text-xs text-slate-400 font-normal">poin</span>
                            </div>
                        </div>

                        <!-- Tombol Like & Dislike -->
                        @auth
                            <div class="flex items-center space-x-2">
                                <!-- Tombol Like (Upvote) -->
                                <button type="button" onclick="castVote(1)" id="btnUpvote"
                                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 {{ ($userVote && $userVote->value === 1) ? 'bg-emerald-500 text-slate-950' : 'bg-white/10 hover:bg-white/20 text-white' }}">
                                    <span>👍</span>
                                    <span id="upvotesCount">{{ $report->upvotes_count }}</span>
                                </button>

                                <!-- Tombol Dislike (Downvote) -->
                                <button type="button" onclick="castVote(-1)" id="btnDownvote"
                                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 {{ ($userVote && $userVote->value === -1) ? 'bg-rose-500 text-white' : 'bg-white/10 hover:bg-white/20 text-white' }}">
                                    <span>👎</span>
                                    <span id="downvotesCount">{{ $report->downvotes_count }}</span>
                                </button>
                            </div>
                        @else
                            <div class="text-right">
                                <a href="{{ route('login') }}" class="text-xs text-emerald-400 hover:underline font-semibold">
                                    Masuk untuk vote &rarr;
                                </a>
                            </div>
                        @endauth
                    </div>
                    <p class="text-[11px] text-slate-400 border-t border-slate-800 pt-2">
                        Vote berfungsi menaikkan ranking postingan ke <strong>Urgent & Critical Tier</strong> agar segera diprioritaskan.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Peta Lokasi Masalah (OpenFreeMap) -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Titik Koordinat Peta (OpenFreeMap)</h3>
                <p class="text-xs text-slate-500">Koordinat: {{ $report->latitude }}, {{ $report->longitude }}</p>
            </div>
            <a href="https://www.openstreetmap.org/?mlat={{ $report->latitude }}&mlon={{ $report->longitude }}#map=17/{{ $report->latitude }}/{{ $report->longitude }}" target="_blank" class="text-xs text-emerald-700 font-semibold hover:underline">
                Buka di OSM &rarr;
            </a>
        </div>
        <div id="reportMap" class="w-full h-72 rounded-2xl border border-slate-200 overflow-hidden"></div>
    </div>

    <!-- Diskusi & Komentar Bertingkat (Nested Comments) -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Diskusi & Respon Warga</h3>
                <p class="text-xs text-slate-500">Ada <span id="commentsCountDisplay">{{ $report->comments_count }}</span> komentar pada laporan ini</p>
            </div>
        </div>

        <!-- Form Tambah Komentar Utama -->
        @auth
            <form id="mainCommentForm" action="{{ route('comments.store', $report, false) }}" method="POST" class="space-y-3" onsubmit="submitCommentAjax(event, this, null)">
                @csrf
                <textarea name="content" rows="3" required placeholder="Tulis komentar atau tanggapan terkait masalah ini (Tag @Sira untuk meminta bantuan AI)..."
                    class="w-full px-4 py-3 rounded-2xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>

                <!-- Pratinjau LaTeX jika terdapat formula matematika -->
                <div class="latex-preview hidden px-4 py-3 bg-emerald-50/60 border border-emerald-200/80 rounded-2xl text-xs text-slate-800 space-y-1.5 shadow-xs">
                    <div class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider flex items-center space-x-1.5">
                        <span>📐</span>
                        <span>Pratinjau Formula (LaTeX):</span>
                    </div>
                    <div class="latex-preview-content font-sans overflow-x-auto text-sm"></div>
                </div>

                <div class="flex items-center justify-between flex-wrap gap-2 pt-1">
                    <div class="flex items-center space-x-1.5 text-xs text-indigo-700 font-medium bg-indigo-50 px-3 py-1.5 rounded-xl border border-indigo-100">
                        <span>🤖</span>
                        <span>Tip: Tag <strong>@Sira</strong> di komentar untuk meminta bantuan atau ringkasan AI</span>
                    </div>
                    <button type="submit" class="submit-btn px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-sm transition flex items-center space-x-1.5">
                        <span>Kirim Komentar</span>
                    </button>
                </div>
            </form>
        @else
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-center text-xs text-slate-600">
                Silakan <a href="{{ route('login') }}" class="font-bold text-emerald-700 hover:underline">Masuk</a> untuk ikut berdiskusi dan memberikan tanggapan.
            </div>
        @endauth

        <!-- Daftar Komentar (Pohon Bertingkat) -->
        <div class="space-y-5 pt-4" id="commentsContainer">
            <p id="emptyCommentsMsg" class="text-xs text-slate-400 text-center py-6 {{ $report->rootComments->isEmpty() ? '' : 'hidden' }}">
                Belum ada komentar. Jadilah yang pertama berkomentar!
            </p>
            <div id="commentsList" class="space-y-5">
                @foreach ($report->rootComments as $comment)
                    @include('reports._comment_item', ['comment' => $comment])
                @endforeach
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
    new maplibregl.Marker({ color: '#E11D48' })
        .setLngLat([reportLng, reportLat])
        .setPopup(new maplibregl.Popup().setHTML('<strong class="text-xs">{{ addslashes($report->title) }}</strong>'))
        .addTo(map);

    // -------------------------------------------------------------
    // Fungsi Toggle Balas Komentar
    // -------------------------------------------------------------
    function toggleReplyForm(commentId) {
        const form = document.getElementById('reply-form-' + commentId);
        if (form) {
            form.classList.toggle('hidden');
            if (!form.classList.contains('hidden')) {
                const ta = form.querySelector('textarea');
                if (ta) ta.focus();
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
            container.innerHTML = `<span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold bg-amber-500 text-white shadow-md">⚠️ URGENT TIER</span>`;
        } else if (tier === 'trending') {
            container.innerHTML = `<span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold bg-teal-600 text-white shadow-md">🔥 TRENDING TIER</span>`;
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
                if (data.status === 'resolved') {
                    badge.className = 'px-2.5 py-1 rounded-full font-bold uppercase tracking-wider transition duration-200 bg-emerald-600 text-white shadow-xs';
                    badge.innerText = 'Status: RESOLVED';
                    if (actionsContainer) {
                        actionsContainer.innerHTML = `
                            <button type="button" onclick="updateReportStatus('active')" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition flex items-center space-x-1" title="Buka kembali laporan ini">
                                <span>↩</span>
                                <span>Buka Kembali</span>
                            </button>
                        `;
                    }
                } else {
                    badge.className = 'px-2.5 py-1 rounded-full font-bold uppercase tracking-wider transition duration-200 bg-emerald-50 text-emerald-700 border border-emerald-200';
                    badge.innerText = 'Status: ' + data.status_label.toUpperCase();
                    if (actionsContainer) {
                        actionsContainer.innerHTML = `
                            <button type="button" onclick="updateReportStatus('resolved')" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition flex items-center space-x-1 shadow-xs" title="Tandai masalah telah terselesaikan">
                                <span>✓</span>
                                <span>Tandai Selesai (Resolved)</span>
                            </button>
                        `;
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
        handleLatexPreview(textarea);
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
                .replace(/`([^`]+)`/g, '<code class="px-1 py-0.5 bg-slate-200/70 text-slate-800 rounded font-mono text-[11px]">$1</code>')
                .replace(/\n/g, '<br>');
        }

        // 5. Kembalikan token LaTeX yang sudah dirender sempurna
        mathTokens.forEach((tokenHtml, idx) => {
            html = html.replaceAll(`%%KATEX_TOKEN_${idx}%%`, tokenHtml);
        });

        // 6. Highlight mention username (Sira bot atau user lain)
        html = html.replace(/(^|[^a-zA-Z0-9_])\x40([a-zA-Z0-9_]+)/g, (match, prefix, username) => {
            const isAi = username.toLowerCase() === 'sira';
            const badge = isAi
                ? `<span class="inline-flex items-center font-bold text-indigo-700 bg-indigo-50 border border-indigo-200/80 px-1.5 py-0.5 rounded-md mx-0.5">@${username}</span>`
                : `<span class="inline-flex items-center font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-1.5 py-0.5 rounded-md mx-0.5">@${username}</span>`;
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

    // Live preview saat pengguna mengetik formula LaTeX atau Markdown
    function handleLatexPreview(textarea) {
        if (!textarea) return;
        const form = textarea.closest('form');
        if (!form) return;
        const previewBox = form.querySelector('.latex-preview');
        const previewContent = form.querySelector('.latex-preview-content');
        if (!previewBox || !previewContent) return;

        const text = textarea.value.trim();
        // Deteksi apakah mengandung simbol LaTeX ($ / \ / ^ / _) atau Markdown (* / `)
        if (text && (text.includes('$') || text.includes('\\') || text.includes('^') || text.includes('_') || text.includes('*') || text.includes('`'))) {
            previewContent.innerHTML = formatCommentText(text);
            previewBox.classList.remove('hidden');
        } else {
            previewBox.classList.add('hidden');
            previewContent.innerHTML = '';
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
                    form.querySelectorAll('.latex-preview').forEach(p => p.classList.add('hidden'));
                    toggleReplyForm(parentId);
                } else {
                    // Komentar utama (root)
                    const commentsList = document.getElementById('commentsList');
                    if (commentsList) {
                        commentsList.insertAdjacentHTML('afterbegin', data.comment_html);
                    }
                    form.reset();
                    form.querySelectorAll('.latex-preview').forEach(p => p.classList.add('hidden'));
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
                                🤖
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
                            indicator.innerHTML = `<span>⚠️</span><span>AI @Sira sedang tidak dapat merespons saat ini. Komentar Anda tetap tersimpan.</span>`;
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
        dropdown.className = 'fixed hidden bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl overflow-hidden w-72 max-w-[90vw] transition-opacity duration-150 text-left';
        dropdown.innerHTML = `
            <div class="px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-700/60 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between">
                <span>Saran Akun & AI</span>
                <span class="text-[9px] font-normal lowercase text-slate-400">Gunakan ↑↓ dan ↵</span>
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
                item.className = `px-2.5 py-2 rounded-xl text-xs cursor-pointer flex items-center justify-between transition ${isSelected ? 'bg-indigo-50 text-indigo-950 font-semibold' : 'hover:bg-slate-50 text-slate-700'}`;
                
                const isAi = user.is_ai;
                item.innerHTML = `
                    <div class="flex items-center space-x-2 min-w-0">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 ${isAi ? 'bg-gradient-to-tr from-indigo-600 to-purple-600 text-white shadow-xs' : 'bg-emerald-100 text-emerald-800 uppercase'}">
                            ${isAi ? '🤖' : (user.username ? user.username.charAt(0) : 'U')}
                        </div>
                        <div class="truncate">
                            <div class="truncate text-xs ${isAi ? 'text-indigo-900 font-bold' : 'text-slate-900 font-medium'}">${user.name}</div>
                            <div class="text-[11px] text-slate-400 font-mono">@${user.username}</div>
                        </div>
                    </div>
                    ${user.badge ? `<span class="ml-2 shrink-0 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-indigo-600 text-white">${user.badge}</span>` : ''}
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
                if (i === highlightedIndex) {
                    items[i].className = 'px-2.5 py-2 rounded-xl text-xs cursor-pointer flex items-center justify-between transition bg-indigo-50 text-indigo-950 font-semibold';
                } else {
                    items[i].className = 'px-2.5 py-2 rounded-xl text-xs cursor-pointer flex items-center justify-between transition hover:bg-slate-50 text-slate-700';
                }
            }
        }

        function positionDropdown() {
            if (!currentTargetTextarea) return;
            const rect = currentTargetTextarea.getBoundingClientRect();
            const dropdownHeight = dropdown.offsetHeight || 220;
            
            // Karena menggunakan fixed, koordinat relative langsung terhadap viewport (tanpa scrollY)
            let top = rect.bottom + 6;
            if (top + dropdownHeight > window.innerHeight && rect.top - dropdownHeight - 6 > 0) {
                top = rect.top - dropdownHeight - 6;
            }

            const left = Math.max(10, Math.min(rect.left, window.innerWidth - 300));

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

        // Listener Delegasi ke semua textarea komentar (input, click, keyup)
        document.addEventListener('input', function(e) {
            handleMentionTrigger(e.target);
            handleLatexPreview(e.target);
        });

        document.addEventListener('click', function(e) {
            if (e.target && e.target.tagName === 'TEXTAREA' && e.target.name === 'content') {
                handleMentionTrigger(e.target);
            } else if (!dropdown.contains(e.target)) {
                closeMentionDropdown();
            }
        });

        document.addEventListener('keyup', function(e) {
            if (['ArrowLeft', 'ArrowRight', 'Home', 'End', 'Backspace'].includes(e.key)) {
                handleMentionTrigger(e.target);
            }
        });

        document.addEventListener('keydown', function(e) {
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
        }
    }, 120);
</script>
@endpush
