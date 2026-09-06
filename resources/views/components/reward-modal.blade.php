@php
    $topReporters = \App\Models\User::query()
        ->whereRaw('LOWER(username) != ?', ['sira'])
        ->withCount('reports')
        ->orderByDesc('reports_count')
        ->take(5)
        ->get();
@endphp

<!-- Komponen Pop-up Poster Reward Top 5 Bulanan SIRA -->
<div id="rewardPosterModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-3 sm:p-4 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="rewardPosterTitle">
    <!-- Backdrop Gelap dengan Efek Blur Halus -->
    <div id="rewardPosterBackdrop" class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeRewardModal()"></div>

    <!-- Kontainer Kartu Poster Interaktif -->
    <div id="rewardPosterCard" class="relative w-full max-w-lg bg-[#FAFAFA] dark:bg-[#141414] border border-slate-200/90 dark:border-[#282828] rounded-3xl shadow-2xl overflow-hidden transition-all duration-300 transform scale-95 opacity-0 z-10 my-auto text-left font-sans">
        <!-- Banner Header Poster Berwarna Dinamis -->
        <div class="relative bg-gradient-to-br from-amber-500 via-amber-600 to-emerald-700 dark:from-amber-600 dark:via-amber-700 dark:to-emerald-900 p-6 sm:p-7 text-white overflow-hidden select-none">
            <!-- Ornamen Pola Latar Belakang -->
            <div class="absolute -right-8 -top-8 w-44 h-44 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
            <div class="absolute -left-8 -bottom-8 w-36 h-36 rounded-full bg-emerald-400/20 blur-xl pointer-events-none"></div>

            <!-- Tombol Tutup X -->
            <button type="button" onclick="closeRewardModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-black/20 hover:bg-black/40 text-white flex items-center justify-center transition cursor-pointer" title="Tutup Poster" aria-label="Tutup">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            <!-- Badge Kategori -->
            <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white text-[11px] font-mono uppercase tracking-widest font-bold mb-3 shadow-xs">
                <flux:icon name="trophy" class="w-3.5 h-3.5 text-white shrink-0" />
                <span>Program Apresiasi Warga</span>
            </div>

            <h2 id="rewardPosterTitle" class="text-2xl sm:text-3xl font-black tracking-tight leading-tight drop-shadow-xs">
                Ayo, Lapor &amp; Dapatkan Hadiah Berlimpah!
            </h2>
            <p class="text-xs sm:text-sm text-amber-100 dark:text-amber-200 mt-2 leading-relaxed">
                Pantau fasilitas umum di sekitar Anda. Setiap laporan valid memperbesar peluang Anda memenangkan reward bulanan eksklusif!
            </p>
        </div>

        <!-- Badan Konten Poster -->
        <div class="p-5 sm:p-6 space-y-5 max-h-[65vh] overflow-y-auto">
            <!-- Parameter Penilaian -->
            <div class="flex items-start space-x-3 p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/20 dark:bg-amber-950/20 dark:border-amber-800/40">
                <div class="w-7 h-7 rounded-xl bg-amber-500 text-slate-950 font-bold flex items-center justify-center shrink-0 shadow-xs">
                    <flux:icon name="chart-bar" class="w-4 h-4 text-slate-950" />
                </div>
                <div class="text-xs">
                    <div class="font-bold text-slate-900 dark:text-[#EDEDEC]">
                        Parameter Penilaian: <span class="text-amber-600 dark:text-amber-400">Jumlah Laporan User</span>
                    </div>
                    <p class="text-slate-600 dark:text-[#A0A0A0] text-[11px] mt-0.5 leading-relaxed">
                        Peringkat dihitung berdasarkan total partisipasi laporan kerusakan fasilitas umum yang Anda kirimkan. Makin aktif Anda melapor, makin tinggi peringkat Anda!
                    </p>
                </div>
            </div>

            <!-- Daftar Hadiah Reward Top 5 -->
            <div class="space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-[#EDEDEC] flex items-center space-x-1.5">
                        <flux:icon name="gift" class="w-4 h-4 text-amber-500 shrink-0" />
                        <span>Reward Top 5 Bulanan</span>
                    </span>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 font-bold">
                        Periode Setiap Bulan
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                    <div class="p-3 rounded-xl bg-gradient-to-r from-amber-500/15 to-amber-600/5 border border-amber-400/40 dark:border-amber-700/50 flex items-center space-x-2.5 sm:col-span-2">
                        <div class="w-8 h-8 rounded-full bg-amber-500/20 text-amber-500 flex items-center justify-center shrink-0">
                            <flux:icon name="trophy" class="w-4 h-4 text-amber-500" />
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-amber-900 dark:text-amber-300">Juara 1: E-Wallet Rp 500.000</div>
                            <div class="text-[11px] text-slate-600 dark:text-[#A0A0A0] truncate">+ Lencana Eksklusif Warga Teladan Gold</div>
                        </div>
                    </div>

                    <div class="p-2.5 rounded-xl bg-slate-100/90 dark:bg-[#1F1F1E] border border-slate-200 dark:border-[#2C2C2B] flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-full bg-slate-200 dark:bg-[#282828] text-slate-500 flex items-center justify-center shrink-0">
                            <flux:icon name="trophy" class="w-3.5 h-3.5 text-slate-400" />
                        </div>
                        <div class="min-w-0">
                            <div class="font-semibold text-slate-900 dark:text-[#EDEDEC] truncate">Juara 2: Rp 300.000</div>
                            <div class="text-[10px] text-slate-500 dark:text-[#888888] truncate">+ Merchandise SIRA</div>
                        </div>
                    </div>

                    <div class="p-2.5 rounded-xl bg-slate-100/90 dark:bg-[#1F1F1E] border border-slate-200 dark:border-[#2C2C2B] flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-full bg-amber-700/20 text-amber-700 flex items-center justify-center shrink-0">
                            <flux:icon name="trophy" class="w-3.5 h-3.5 text-amber-700" />
                        </div>
                        <div class="min-w-0">
                            <div class="font-semibold text-slate-900 dark:text-[#EDEDEC] truncate">Juara 3: Rp 200.000</div>
                            <div class="text-[10px] text-slate-500 dark:text-[#888888] truncate">+ Merchandise SIRA</div>
                        </div>
                    </div>

                    <div class="p-2.5 rounded-xl bg-slate-100/90 dark:bg-[#1F1F1E] border border-slate-200 dark:border-[#2C2C2B] flex items-center space-x-2">
                        <span class="text-xs font-bold text-sky-600 dark:text-sky-400 px-1.5 shrink-0 font-mono">#4</span>
                        <div class="min-w-0">
                            <div class="font-medium text-slate-800 dark:text-[#D4D4D4] truncate">Juara 4: Rp 100.000</div>
                            <div class="text-[10px] text-slate-500 dark:text-[#888888]">Saldo E-Wallet</div>
                        </div>
                    </div>

                    <div class="p-2.5 rounded-xl bg-slate-100/90 dark:bg-[#1F1F1E] border border-slate-200 dark:border-[#2C2C2B] flex items-center space-x-2">
                        <span class="text-xs font-bold text-sky-600 dark:text-sky-400 px-1.5 shrink-0 font-mono">#5</span>
                        <div class="min-w-0">
                            <div class="font-medium text-slate-800 dark:text-[#D4D4D4] truncate">Juara 5: Rp 100.000</div>
                            <div class="text-[10px] text-slate-500 dark:text-[#888888]">Saldo E-Wallet</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Leaderboard Saat Ini (Top 5 Pelapor Teraktif) -->
            <div class="space-y-2 pt-1 border-t border-slate-200/80 dark:border-[#242424]">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-[#EDEDEC] flex items-center space-x-1.5">
                        <flux:icon name="sparkles" class="w-3.5 h-3.5 text-amber-500 shrink-0" />
                        <span>Klasemen Terkini (Top 5 Pelapor)</span>
                    </span>
                    <span class="text-[10px] font-mono text-slate-400 dark:text-[#777777]">Live Update</span>
                </div>

                <div class="space-y-1.5">
                    @forelse ($topReporters as $idx => $reporter)
                        <div class="flex items-center justify-between p-2.5 rounded-xl {{ $idx === 0 ? 'bg-amber-500/10 border border-amber-500/20' : 'bg-white dark:bg-[#1A1A19] border border-slate-200/70 dark:border-[#282828]' }}">
                            <div class="flex items-center space-x-2.5 min-w-0">
                                <span class="font-mono text-xs font-bold w-5 flex items-center justify-center shrink-0 {{ $idx === 0 ? 'text-amber-500' : ($idx === 1 ? 'text-slate-400' : ($idx === 2 ? 'text-amber-700' : 'text-slate-500')) }}">
                                    @if ($idx === 0)
                                        <flux:icon name="trophy" class="w-3.5 h-3.5 text-amber-500" />
                                    @elseif ($idx === 1)
                                        <flux:icon name="trophy" class="w-3.5 h-3.5 text-slate-400" />
                                    @elseif ($idx === 2)
                                        <flux:icon name="trophy" class="w-3.5 h-3.5 text-amber-700" />
                                    @else
                                        #{{ $idx + 1 }}
                                    @endif
                                </span>
                                <div class="inline-flex items-center space-x-1 min-w-0">
                                    <span class="text-xs font-bold text-slate-900 dark:text-[#EDEDEC] truncate">@<span>{{ $reporter->username }}</span></span>
                                    <x-verified-badge :user="$reporter" size="xs" />
                                </div>
                            </div>
                            <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 shrink-0">
                                {{ $reporter->reports_count }} <span class="text-[10px] font-normal text-slate-500 dark:text-[#888888]">Laporan</span>
                            </span>
                        </div>
                    @empty
                        <p class="text-center py-3 text-xs text-slate-400 font-mono">Belum ada data pelapor.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Footer Aksi & CTA -->
        <div class="p-4 sm:p-5 bg-slate-100 dark:bg-[#181818] border-t border-slate-200 dark:border-[#262626] flex flex-col sm:flex-row items-center justify-between gap-3">
            <label class="flex items-center space-x-2 text-[11px] font-mono text-slate-500 dark:text-[#888888] select-none cursor-pointer">
                <input type="checkbox" id="dontShowAgainCheck" onchange="toggleRewardModalPreference(this)" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <span>Jangan tampilkan lagi hari ini</span>
            </label>

            <div class="flex items-center space-x-2 w-full sm:w-auto">
                <button type="button" onclick="closeRewardModal()" class="w-1/2 sm:w-auto px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-200 dark:text-[#CCCCCC] dark:hover:bg-[#252525] transition cursor-pointer">
                    Tutup
                </button>
                <a href="{{ route('reports.create') }}" class="w-1/2 sm:w-auto px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition flex items-center justify-center space-x-1.5 shadow-sm active:scale-95">
                    <span>Lapor Sekarang &rarr;</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Tombol Melayang untuk Membuka Kembali Poster Reward Kapan Saja -->
<div class="fixed bottom-5 left-5 z-[9990] select-none">
    <button type="button" onclick="openRewardModal(true)"
        class="group flex items-center space-x-2 px-3.5 py-2.5 rounded-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white text-xs font-bold shadow-lg hover:shadow-xl transition-all duration-200 cursor-pointer border border-amber-300/40 active:scale-95">
        <flux:icon name="gift" class="w-4 h-4 text-white shrink-0 group-hover:scale-110 transition-transform duration-200" />
        <span class="hidden sm:inline font-sans">Info Reward Top 5</span>
        <span class="sm:hidden font-sans">Reward</span>
    </button>
</div>

<!-- Script Pengendali Pop-Up Poster Reward -->
<script>
    function openRewardModal(isManual = false) {
        const modal = document.getElementById('rewardPosterModal');
        const backdrop = document.getElementById('rewardPosterBackdrop');
        const card = document.getElementById('rewardPosterCard');
        if (!modal || !backdrop || !card) return;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');

        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');
        });
    }

    function closeRewardModal() {
        const modal = document.getElementById('rewardPosterModal');
        const backdrop = document.getElementById('rewardPosterBackdrop');
        const card = document.getElementById('rewardPosterCard');
        if (!modal || !backdrop || !card) return;

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        card.classList.remove('scale-100', 'opacity-100');
        card.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }, 300);
    }

    function toggleRewardModalPreference(checkbox) {
        if (checkbox.checked) {
            localStorage.setItem('sira_hide_reward_popup_until', String(Date.now() + 24 * 60 * 60 * 1000));
        } else {
            localStorage.removeItem('sira_hide_reward_popup_until');
        }
    }

    // Munculkan Pop Up Poster Otomatis Saat Pertama Masuk ke Web
    document.addEventListener('DOMContentLoaded', function () {
        const hideUntil = localStorage.getItem('sira_hide_reward_popup_until');
        const sessionSeen = sessionStorage.getItem('sira_reward_popup_seen');

        if (hideUntil && Number(hideUntil) > Date.now()) {
            // Pengguna mencentang 'jangan tampilkan lagi hari ini'
            return;
        }

        if (!sessionSeen) {
            sessionStorage.setItem('sira_reward_popup_seen', 'true');
            // Beri sedikit jeda halus (600ms) agar halaman web render sempurna terlebih dahulu
            setTimeout(() => {
                openRewardModal(false);
            }, 600);
        }
    });

    // Dukung tombol keyboard ESC untuk menutup modal
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('rewardPosterModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeRewardModal();
            }
        }
    });
</script>
