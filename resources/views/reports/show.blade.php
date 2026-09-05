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
                    <!-- Status & Waktu -->
                    <div class="flex items-center justify-between text-xs">
                        <span class="px-2.5 py-1 rounded-full font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Status: {{ str_replace('_', ' ', $report->status) }}
                        </span>
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
                <p class="text-xs text-slate-500">Ada {{ $report->comments_count }} komentar pada laporan ini</p>
            </div>
        </div>

        <!-- Form Tambah Komentar Utama -->
        @auth
            <form action="{{ route('comments.store', $report) }}" method="POST" class="space-y-3">
                @csrf
                <textarea name="content" rows="3" required placeholder="Tulis komentar atau informasi tambahan terkait masalah ini..."
                    class="w-full px-4 py-3 rounded-2xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-sm transition">
                        Kirim Komentar
                    </button>
                </div>
            </form>
        @else
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-center text-xs text-slate-600">
                Silakan <a href="{{ route('login') }}" class="font-bold text-emerald-700 hover:underline">Masuk</a> untuk ikut berdiskusi dan memberikan tanggapan.
            </div>
        @endauth

        <!-- Daftar Komentar (Pohon Bertingkat) -->
        <div class="space-y-5 pt-4">
            @if ($report->rootComments->isEmpty())
                <p class="text-xs text-slate-400 text-center py-6">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
            @else
                @foreach ($report->rootComments as $comment)
                    @include('reports._comment_item', ['comment' => $comment])
                @endforeach
            @endif
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
        }
    }

    // -------------------------------------------------------------
    // Voting AJAX Interaktif (Like & Dislike)
    // -------------------------------------------------------------
    function castVote(val) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const btnUpvote = document.getElementById('btnUpvote');
        const btnDownvote = document.getElementById('btnDownvote');

        fetch("{{ route('reports.vote', $report) }}", {
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
            container.innerHTML = `<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-800/80 backdrop-blur-md text-white">NORMAL TIER</span>`;
        }
    }
</script>
@endpush
