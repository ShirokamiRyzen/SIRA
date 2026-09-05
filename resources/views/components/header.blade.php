<!-- Component: Header Navigation (Minimalist Utilitarian) -->
<header class="bg-[#FBFBFA]/85 dark:bg-[#0E0E0E]/85 backdrop-blur-md border-b border-[#EAEAEA] dark:border-[#202020] sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Brand Logo -->
        <div class="flex items-center space-x-6">
            <a href="{{ url('/') }}" class="flex items-center space-x-2.5 group">
                <span class="w-7 h-7 rounded-[4px] bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111] flex items-center justify-center font-mono text-xs font-semibold">
                    S
                </span>
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-semibold tracking-tight text-[#111111] dark:text-[#EDEDEC]">SIRA</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#EDF3EC] text-[#346538] dark:bg-[#1C281E] dark:text-[#82C78A]">
                        Pengawasan Warga
                    </span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center space-x-1 font-mono text-xs">
                <a href="{{ route('reports.index') }}" class="px-3 py-1.5 rounded-[6px] transition-colors {{ request()->routeIs('reports.index') ? 'text-[#111111] dark:text-[#EDEDEC] bg-[#EAEAEA]/80 dark:bg-[#222222] font-medium' : 'text-[#787774] dark:text-[#9B9B97] hover:text-[#111111] dark:hover:text-[#EDEDEC] hover:bg-[#EAEAEA]/40 dark:hover:bg-[#1E1E1E]' }}">
                    Dasbor Laporan
                </a>
                <a href="{{ route('heatmap.index') }}" class="px-3 py-1.5 rounded-[6px] transition-colors flex items-center space-x-1.5 {{ request()->routeIs('heatmap.index') ? 'text-[#111111] dark:text-[#EDEDEC] bg-[#EAEAEA]/80 dark:bg-[#222222] font-medium' : 'text-[#787774] dark:text-[#9B9B97] hover:text-[#111111] dark:hover:text-[#EDEDEC] hover:bg-[#EAEAEA]/40 dark:hover:bg-[#1E1E1E]' }}">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#9F2F2D]"></span>
                    <span>Peta Sebaran</span>
                </a>
                <a href="{{ url('/#cara-kerja') }}" class="px-3 py-1.5 rounded-[6px] transition-colors text-[#787774] dark:text-[#9B9B97] hover:text-[#111111] dark:hover:text-[#EDEDEC] hover:bg-[#EAEAEA]/40 dark:hover:bg-[#1E1E1E] hidden lg:inline-block">
                    Cara Kerja
                </a>
                <a href="{{ url('/#fitur') }}" class="px-3 py-1.5 rounded-[6px] transition-colors text-[#787774] dark:text-[#9B9B97] hover:text-[#111111] dark:hover:text-[#EDEDEC] hover:bg-[#EAEAEA]/40 dark:hover:bg-[#1E1E1E] hidden lg:inline-block">
                    Fitur Utama
                </a>
            </nav>
        </div>

        <!-- Action & Auth Navigation -->
        <div class="flex items-center space-x-2.5 sm:space-x-3 font-mono text-xs">
            <!-- Tombol Pengalih Tema (Light / Dark) -->
            <button
                id="theme-toggle"
                type="button"
                aria-label="Ganti tema warna"
                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#161615] text-[#111111] dark:text-[#EDEDEC] hover:bg-[#F7F6F3] dark:hover:bg-[#1F1F1E] transition-all active:scale-[0.98] font-mono text-[11px] cursor-pointer"
            >
                <svg id="theme-toggle-light-icon" class="w-3.5 h-3.5 hidden" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75">
                    <circle cx="8" cy="8" r="3"/>
                    <path d="M8 1.5v1.5M8 13v1.5M1.5 8h1.5M13 8h1.5M3.4 3.4l1.1 1.1M11.5 11.5l1.1 1.1M3.4 12.6l1.1-1.1M11.5 4.5l1.1-1.1" stroke-linecap="square"/>
                </svg>
                <svg id="theme-toggle-dark-icon" class="w-3.5 h-3.5 hidden" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75">
                    <path d="M13.5 9.8A6 6 0 1 1 6.2 2.5a4.8 4.8 0 0 0 7.3 7.3z" stroke-linecap="square"/>
                </svg>
                <span id="theme-toggle-text">Tema</span>
            </button>

            <a href="{{ route('reports.create') }}" class="inline-flex items-center space-x-1 bg-[#111111] hover:bg-[#2A2A2A] active:scale-[0.98] text-white dark:bg-[#EDEDEC] dark:text-[#111111] dark:hover:bg-white text-xs font-medium px-3.5 py-1.5 rounded-[6px] transition duration-150 shrink-0">
                <span>+ Buat Laporan</span>
            </a>

            @auth
                <!-- Notification Bell & Dropdown Panel -->
                @php
                    /** @var \App\Models\User $user */
                    $user = Auth::user();
                    $unreadCount = $user->unreadNotifications()->count();
                    $recentNotifications = $user->notifications()->take(6)->get();
                @endphp
                <div class="relative" id="notificationDropdownContainer">
                    <button
                        type="button"
                        id="notificationBellBtn"
                        aria-label="Lihat notifikasi"
                        class="relative p-2 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#161615] text-[#111111] dark:text-[#EDEDEC] hover:bg-[#F7F6F3] dark:hover:bg-[#1F1F1E] transition-all active:scale-[0.98] cursor-pointer flex items-center justify-center"
                    >
                        <svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75">
                            <path d="M8 1.5A3.5 3.5 0 0 0 4.5 5v2.25L3 9.5v1h10v-1l-1.5-2.25V5A3.5 3.5 0 0 0 8 1.5Z" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6.5 12.5a1.5 1.5 0 0 0 3 0" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>

                        <!-- Notification Count Badge -->
                        <span id="notificationBadge" class="{{ $unreadCount > 0 ? '' : 'hidden' }} absolute -top-1.5 -right-1.5 flex h-4 min-w-4 px-1 items-center justify-center rounded-full bg-[#9F2F2D] text-[9px] font-bold text-white shadow-xs font-mono">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="notificationMenu" class="hidden absolute right-0 mt-2 w-80 sm:w-92 max-w-[92vw] bg-white dark:bg-[#141414] border border-[#EAEAEA] dark:border-[#262626] rounded-[8px] shadow-2xl z-50 overflow-hidden font-sans text-xs">
                        <div class="px-4 py-3 border-b border-[#EAEAEA] dark:border-[#262626] flex items-center justify-between bg-[#FBFBFA] dark:bg-[#181818]">
                            <div class="flex items-center space-x-2">
                                <span class="font-semibold text-[#111111] dark:text-[#EDEDEC]">Notifikasi</span>
                                <span id="notificationUnreadPill" class="{{ $unreadCount > 0 ? '' : 'hidden' }} px-1.5 py-0.5 rounded-full text-[10px] font-mono bg-[#FDEBEC] text-[#9F2F2D] dark:bg-[#311617] dark:text-[#E88C8A]">
                                    <span id="notificationUnreadCount">{{ $unreadCount }}</span> baru
                                </span>
                            </div>

                            <div class="flex items-center space-x-2.5 text-[11px] font-mono">
                                <button type="button" id="markAllReadBtn" title="Tandai semua notifikasi telah dibaca" class="text-[#787774] hover:text-[#111111] dark:text-[#9B9B97] dark:hover:text-[#EDEDEC] transition-colors {{ $unreadCount > 0 ? '' : 'hidden' }}">
                                    Tandai dibaca
                                </button>
                                <button type="button" id="clearAllNotificationsBtn" title="Bersihkan semua riwayat notifikasi" class="text-[#787774] hover:text-[#9F2F2D] dark:text-[#9B9B97] dark:hover:text-[#E88C8A] transition-colors {{ $recentNotifications->isNotEmpty() ? '' : 'hidden' }}">
                                    Hapus semua
                                </button>
                            </div>
                        </div>

                        <!-- Notification List Items -->
                        <div id="notificationList" class="max-h-80 overflow-y-auto divide-y divide-[#EAEAEA]/70 dark:divide-[#262626]/70">
                            @forelse ($recentNotifications as $notification)
                                @php
                                    $data = $notification->data;
                                    $isUnread = $notification->unread();
                                    $type = $data['type'] ?? 'reply';
                                    $isAi = $data['is_ai'] ?? false;
                                @endphp
                                <div id="notif-item-{{ $notification->id }}"
                                     data-unread="{{ $isUnread ? '1' : '0' }}"
                                     class="group relative flex items-start justify-between p-3 hover:bg-[#F7F6F3] dark:hover:bg-[#1C1C1C] transition duration-150 {{ $isUnread ? 'bg-[#F9F9F8] dark:bg-[#191918]' : '' }}">
                                    <a href="{{ route('notifications.markAsRead', ['id' => $notification->id, 'redirect' => 1]) }}"
                                       class="flex items-start space-x-2.5 flex-1 min-w-0 pr-2">
                                        <div class="w-6 h-6 rounded-full shrink-0 flex items-center justify-center text-[10px] {{ $isAi ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300 font-bold' : ($type === 'mention' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300') }}">
                                            @if ($isAi)
                                                🤖
                                            @elseif ($type === 'mention')
                                                @
                                            @else
                                                💬
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0 space-y-0.5">
                                            <p class="text-xs text-[#111111] dark:text-[#EDEDEC] leading-snug">
                                                {{ $data['message'] ?? 'Pembaruan pada laporan' }}
                                            </p>
                                            @if (!empty($data['snippet']))
                                                <p class="text-[11px] text-[#787774] dark:text-[#888888] truncate italic">
                                                    "{{ $data['snippet'] }}"
                                                </p>
                                            @endif
                                            <div class="flex items-center justify-between text-[10px] font-mono text-[#999999] pt-0.5">
                                                <span class="truncate max-w-[150px] sm:max-w-[180px]">{{ $data['report_title'] ?? '' }}</span>
                                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        @if ($isUnread)
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#9F2F2D] shrink-0 mt-1.5 notif-unread-dot"></span>
                                        @endif
                                    </a>

                                    <!-- Tombol Hapus per Notifikasi -->
                                    <button type="button"
                                            onclick="deleteSingleNotification(event, '{{ $notification->id }}')"
                                            title="Hapus notifikasi ini"
                                            class="opacity-0 group-hover:opacity-100 focus:opacity-100 p-1 text-[#999999] hover:text-[#9F2F2D] dark:text-[#666666] dark:hover:text-[#E88C8A] rounded transition-all shrink-0">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75">
                                            <path d="M3 4.5h10M6 7v4.5M10 7v4.5M4.5 4.5V13a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1V4.5M5.5 4.5V3a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </div>
                            @empty
                                <div id="notificationEmptyNotice" class="p-6 text-center text-xs text-[#787774] dark:text-[#888888] font-mono">
                                    Belum ada notifikasi baru.
                                </div>
                            @endforelse
                            <div id="notificationEmptyNoticeFallback" class="hidden p-6 text-center text-xs text-[#787774] dark:text-[#888888] font-mono">
                                Belum ada notifikasi baru.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-2 sm:space-x-3 pl-2 sm:pl-3 border-l border-[#EAEAEA] dark:border-[#282828]">
                    <span class="text-[#787774] dark:text-[#9B9B97] text-[11px] hidden sm:inline">@<span>{{ Auth::user()->username }}</span></span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="Keluar" class="text-[#787774] hover:text-[#9F2F2D] dark:hover:text-[#E88C8A] transition-colors">
                            [Keluar]
                        </button>
                    </form>
                </div>

                <!-- Script Dropdown Notifikasi, Delete, & Clear All -->
                <script>
                    (function () {
                        const btn = document.getElementById('notificationBellBtn');
                        const menu = document.getElementById('notificationMenu');
                        const container = document.getElementById('notificationDropdownContainer');
                        const markAllBtn = document.getElementById('markAllReadBtn');
                        const clearAllBtn = document.getElementById('clearAllNotificationsBtn');
                        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

                        if (btn && menu) {
                            btn.addEventListener('click', function (e) {
                                e.stopPropagation();
                                menu.classList.toggle('hidden');
                            });

                            document.addEventListener('click', function (e) {
                                if (container && !container.contains(e.target)) {
                                    menu.classList.add('hidden');
                                }
                            });
                        }

                        // Helper sinkronisasi badge tampilan notifikasi
                        window.syncNotificationBadges = function (unreadCount, totalCount) {
                            const badge = document.getElementById('notificationBadge');
                            const unreadPill = document.getElementById('notificationUnreadPill');
                            const unreadCounter = document.getElementById('notificationUnreadCount');
                            const empty1 = document.getElementById('notificationEmptyNotice');
                            const empty2 = document.getElementById('notificationEmptyNoticeFallback');

                            if (unreadCounter) unreadCounter.textContent = unreadCount;

                            if (unreadCount <= 0) {
                                if (badge) badge.classList.add('hidden');
                                if (unreadPill) unreadPill.classList.add('hidden');
                                if (markAllBtn) markAllBtn.classList.add('hidden');
                            } else {
                                if (badge) badge.classList.remove('hidden');
                                if (unreadPill) unreadPill.classList.remove('hidden');
                                if (markAllBtn) markAllBtn.classList.remove('hidden');
                            }

                            const remainingItems = document.querySelectorAll('#notificationList [id^="notif-item-"]');
                            if (remainingItems.length === 0 || totalCount === 0) {
                                if (empty1) empty1.classList.remove('hidden');
                                if (empty2) empty2.classList.remove('hidden');
                                if (clearAllBtn) clearAllBtn.classList.add('hidden');
                                if (markAllBtn) markAllBtn.classList.add('hidden');
                            }
                        };

                        // Hapus satu notifikasi (Delete per Notif)
                        window.deleteSingleNotification = function (e, id) {
                            if (e) {
                                e.preventDefault();
                                e.stopPropagation();
                            }

                            const el = document.getElementById('notif-item-' + id);
                            if (el) {
                                el.style.transition = 'all 0.2s ease-out';
                                el.style.opacity = '0.3';
                            }

                            fetch(`/notifications/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrf,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success && el) {
                                    el.style.maxHeight = '0px';
                                    el.style.opacity = '0';
                                    el.style.padding = '0px';
                                    setTimeout(() => {
                                        el.remove();
                                        window.syncNotificationBadges(data.unread_count, data.total_count);
                                    }, 200);
                                }
                            })
                            .catch(err => {
                                console.error('Error deleting notification:', err);
                                if (el) el.style.opacity = '1';
                            });
                        };

                        // Tandai semua dibaca
                        if (markAllBtn) {
                            markAllBtn.addEventListener('click', function (e) {
                                e.preventDefault();
                                e.stopPropagation();

                                fetch("{{ route('notifications.markAllAsRead') }}", {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': csrf,
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json',
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        document.querySelectorAll('#notificationList .bg-\\[\\#F9F9F8\\], #notificationList .dark\\:bg-\\[\\#191918\\]').forEach(el => {
                                            el.classList.remove('bg-[#F9F9F8]', 'dark:bg-[#191918]');
                                        });
                                        document.querySelectorAll('#notificationList .notif-unread-dot').forEach(dot => dot.remove());
                                        window.syncNotificationBadges(0);
                                    }
                                })
                                .catch(err => console.error('Error marking all as read:', err));
                            });
                        }

                        // Hapus semua notifikasi (Clear All)
                        if (clearAllBtn) {
                            clearAllBtn.addEventListener('click', function (e) {
                                e.preventDefault();
                                e.stopPropagation();

                                fetch("{{ route('notifications.clearAll') }}", {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': csrf,
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json',
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        document.querySelectorAll('#notificationList [id^="notif-item-"]').forEach(el => el.remove());
                                        window.syncNotificationBadges(0, 0);
                                    }
                                })
                                .catch(err => console.error('Error clearing all notifications:', err));
                            });
                        }

                        // Escape HTML untuk mencegah XSS pada notifikasi realtime
                        function escapeHtml(str) {
                            if (!str) return '';
                            return String(str)
                                .replace(/&/g, '&amp;')
                                .replace(/</g, '&lt;')
                                .replace(/>/g, '&gt;')
                                .replace(/"/g, '&quot;')
                                .replace(/'/g, '&#039;');
                        }

                        // Animasi getar halus pada lonceng saat ada notifikasi masuk
                        function ringBellAnimation() {
                            const bell = document.getElementById('notificationBellBtn');
                            if (!bell) return;
                            bell.classList.add('ring-2', 'ring-[#9F2F2D]', 'scale-110');
                            setTimeout(() => {
                                bell.classList.remove('ring-2', 'ring-[#9F2F2D]', 'scale-110');
                            }, 800);
                        }

                        // Sisipkan notifikasi baru ke dropdown secara dinamis
                        function prependNotificationToDom(item) {
                            const list = document.getElementById('notificationList');
                            if (!list) return;

                            const empty1 = document.getElementById('notificationEmptyNotice');
                            const empty2 = document.getElementById('notificationEmptyNoticeFallback');
                            if (empty1) empty1.classList.add('hidden');
                            if (empty2) empty2.classList.add('hidden');

                            if (clearAllBtn) clearAllBtn.classList.remove('hidden');

                            const d = item.data || {};
                            const isAi = d.is_ai || false;
                            const type = d.type || 'reply';
                            const icon = isAi ? '🤖' : (type === 'mention' ? '@' : '💬');
                            const iconCls = isAi
                                ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300 font-bold'
                                : (type === 'mention' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300');

                            const div = document.createElement('div');
                            div.id = 'notif-item-' + item.id;
                            div.setAttribute('data-unread', '1');
                            div.className = 'group relative flex items-start justify-between p-3 hover:bg-[#F7F6F3] dark:hover:bg-[#1C1C1C] transition duration-200 bg-[#F9F9F8] dark:bg-[#191918]';

                            div.innerHTML = `
                                <a href="/notifications/${item.id}/read?redirect=1" class="flex items-start space-x-2.5 flex-1 min-w-0 pr-2">
                                    <div class="w-6 h-6 rounded-full shrink-0 flex items-center justify-center text-[10px] ${iconCls}">
                                        ${icon}
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-0.5">
                                        <p class="text-xs text-[#111111] dark:text-[#EDEDEC] leading-snug font-medium">
                                            ${escapeHtml(d.message || 'Pembaruan pada laporan')}
                                        </p>
                                        ${d.snippet ? `<p class="text-[11px] text-[#787774] dark:text-[#888888] truncate italic">"${escapeHtml(d.snippet)}"</p>` : ''}
                                        <div class="flex items-center justify-between text-[10px] font-mono text-[#999999] pt-0.5">
                                            <span class="truncate max-w-[150px] sm:max-w-[180px]">${escapeHtml(d.report_title || '')}</span>
                                            <span>Baru saja</span>
                                        </div>
                                    </div>
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#9F2F2D] shrink-0 mt-1.5 notif-unread-dot"></span>
                                </a>
                                <button type="button"
                                        onclick="deleteSingleNotification(event, '${item.id}')"
                                        title="Hapus notifikasi ini"
                                        class="opacity-0 group-hover:opacity-100 focus:opacity-100 p-1 text-[#999999] hover:text-[#9F2F2D] dark:text-[#666666] dark:hover:text-[#E88C8A] rounded transition-all shrink-0">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75">
                                        <path d="M3 4.5h10M6 7v4.5M10 7v4.5M4.5 4.5V13a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1V4.5M5.5 4.5V3a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            `;

                            list.prepend(div);
                        }

                        // Menampilkan Toast notifikasi realtime melayang di pojok kanan bawah
                        function showRealtimeNotificationToast(item) {
                            const container = document.getElementById('realtimeNotificationToastContainer');
                            if (!container) return;

                            const d = item.data || {};
                            const isAi = d.is_ai || false;
                            const type = d.type || 'reply';
                            const icon = isAi ? '🤖' : (type === 'mention' ? '@' : '💬');

                            const toast = document.createElement('div');
                            toast.className = 'pointer-events-auto flex items-start space-x-3 p-3.5 rounded-[8px] bg-white dark:bg-[#141414] border border-[#EAEAEA] dark:border-[#282828] shadow-xl text-xs transform translate-y-4 opacity-0 transition-all duration-300 max-w-sm';

                            toast.innerHTML = `
                                <div class="w-7 h-7 rounded-full shrink-0 flex items-center justify-center text-xs font-bold ${isAi ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : (type === 'mention' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300')}">
                                    ${icon}
                                </div>
                                <div class="flex-1 min-w-0 space-y-0.5">
                                    <div class="flex items-center justify-between">
                                        <span class="font-mono text-[10px] uppercase font-bold tracking-wider text-[#9F2F2D]">Notifikasi Baru</span>
                                        <button type="button" class="text-[#999999] hover:text-[#111111] dark:hover:text-white toast-close-btn">&times;</button>
                                    </div>
                                    <a href="/notifications/${item.id}/read?redirect=1" class="block font-medium text-[#111111] dark:text-[#EDEDEC] hover:underline leading-snug">
                                        ${escapeHtml(d.message || 'Pembaruan baru')}
                                    </a>
                                    ${d.snippet ? `<p class="text-[11px] text-[#787774] dark:text-[#888888] truncate italic">"${escapeHtml(d.snippet)}"</p>` : ''}
                                </div>
                            `;

                            container.appendChild(toast);

                            // Animasi masuk
                            requestAnimationFrame(() => {
                                toast.classList.remove('translate-y-4', 'opacity-0');
                            });

                            const closeBtn = toast.querySelector('.toast-close-btn');
                            const removeToast = () => {
                                toast.classList.add('opacity-0', 'translate-x-4');
                                setTimeout(() => toast.remove(), 300);
                            };

                            if (closeBtn) closeBtn.addEventListener('click', removeToast);
                            setTimeout(removeToast, 6000);
                        }

                        // Hubungkan koneksi Realtime Server-Sent Events (SSE)
                        let eventSource = null;
                        function initNotificationStream() {
                            if (!window.EventSource) return;

                            if (eventSource) {
                                eventSource.close();
                            }

                            try {
                                eventSource = new EventSource("{{ route('notifications.stream') }}");

                                eventSource.addEventListener('notification', function (e) {
                                    try {
                                        const data = JSON.parse(e.data);
                                        window.syncNotificationBadges(data.unread_count, data.total_count);
                                        if (!document.getElementById('notif-item-' + data.id)) {
                                            prependNotificationToDom(data);
                                        }
                                        ringBellAnimation();
                                        showRealtimeNotificationToast(data);
                                    } catch (err) {
                                        console.error('Error handling SSE notification:', err);
                                    }
                                });

                                eventSource.addEventListener('unread_count', function (e) {
                                    try {
                                        const data = JSON.parse(e.data);
                                        window.syncNotificationBadges(data.unread_count, data.total_count);
                                    } catch (err) {
                                        console.error('Error handling SSE unread_count:', err);
                                    }
                                });
                            } catch (err) {
                                console.warn('Koneksi notifikasi realtime stream gagal diinisialisasi:', err);
                            }
                        }

                        initNotificationStream();

                        window.addEventListener('beforeunload', function () {
                            if (eventSource) {
                                eventSource.close();
                            }
                        });
                    })();
                </script>
            @else
                <div class="flex items-center space-x-1.5 sm:space-x-2 pl-2 sm:pl-3 border-l border-[#EAEAEA] dark:border-[#282828]">
                    <a href="{{ route('login') }}" class="text-[#787774] hover:text-[#111111] dark:text-[#9B9B97] dark:hover:text-[#EDEDEC] px-2 py-1.5 transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="text-[#111111] dark:text-[#111111] bg-[#EAEAEA] hover:bg-[#E0E0E0] dark:bg-[#EDEDEC] dark:hover:bg-white px-2.5 py-1.5 rounded-[6px] font-medium transition-colors">
                        Daftar
                    </a>
                </div>
            @endauth
        </div>
    </div>
    <!-- Wadah Toast Notifikasi Realtime -->
    <div id="realtimeNotificationToastContainer" class="fixed bottom-5 right-5 z-[9999] flex flex-col space-y-2 pointer-events-none max-w-sm w-full px-4 sm:px-0"></div>
</header>
