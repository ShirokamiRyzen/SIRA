<!-- Component: Header Navigation (Minimalist Utilitarian) -->
<header class="bg-sand-25/85 dark:bg-sand-1000/85 backdrop-blur-md border-b border-sand-100 dark:border-sand-925 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Brand Logo -->
        <div class="flex items-center space-x-6">
            <a href="{{ url('/') }}" class="flex items-center space-x-2.5 group" title="SIRA — Sistem Informasi Ruang Aman">
                <img src="{{ asset('android-chrome-512x512.png') }}" alt="SIRA Logo" class="w-7 h-7 rounded-md object-contain shrink-0">
                <span class="text-sm font-semibold tracking-tight text-sand-975 dark:text-sand-100">SIRA</span>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center space-x-1 font-mono text-xs">
                <!-- Dropdown Laporan -->
                <div class="relative" id="reportsNavDropdownContainer">
                    <button
                        type="button"
                        id="reportsNavDropdownBtn"
                        aria-expanded="false"
                        class="px-3 py-1.5 rounded-md transition-colors flex items-center space-x-1.5 cursor-pointer {{ request()->routeIs('reports.index') ? 'text-sand-975 dark:text-sand-100 bg-sand-100/80 dark:bg-sand-900 font-semibold' : 'text-sand-600 dark:text-sand-500 hover:text-sand-975 dark:hover:text-sand-100 hover:bg-sand-100/40 dark:hover:bg-sand-925' }}"
                    >
                        <flux:icon name="document-text" class="w-3.5 h-3.5 shrink-0 text-sand-600 dark:text-sand-500" />
                        <span>Laporan</span>
                        <flux:icon name="chevron-down" id="reportsNavChevron" class="w-3 h-3 text-sand-600 dark:text-sand-500 transition-transform duration-200" />
                    </button>

                    <div
                        id="reportsNavDropdownMenu"
                        class="hidden absolute left-0 mt-1.5 w-48 rounded-xl bg-white dark:bg-sand-950 border border-sand-100 dark:border-sand-850 shadow-lg py-1 z-50 transition-all font-mono"
                    >
                        <a href="{{ route('reports.index') }}"
                           class="flex items-center space-x-2 px-3 py-2 text-xs text-sand-975 dark:text-sand-100 hover:bg-sand-50 dark:hover:bg-sand-925 transition {{ request()->routeIs('reports.index') && !request('my_reports') ? 'bg-sand-50 dark:bg-sand-925 font-bold' : '' }}">
                            <flux:icon name="queue-list" class="w-3.5 h-3.5 text-sand-600 dark:text-sand-500 shrink-0" />
                            <span>Semua Laporan</span>
                        </a>

                        @auth
                            <a href="{{ route('reports.index', ['my_reports' => 1]) }}"
                               class="flex items-center space-x-2 px-3 py-2 text-xs text-sand-975 dark:text-sand-100 hover:bg-sand-50 dark:hover:bg-sand-925 transition {{ request()->routeIs('reports.index') && request('my_reports') ? 'bg-sand-50 dark:bg-sand-925 font-bold text-emerald-600 dark:text-emerald-400' : '' }}">
                                <flux:icon name="user" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                                <span class="text-emerald-700 dark:text-emerald-400 font-medium">Laporan Saya</span>
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Peta Sebaran Masalah -->
                <a href="{{ route('heatmap.index') }}" class="px-3 py-1.5 rounded-md transition-colors flex items-center space-x-1.5 {{ request()->routeIs('heatmap.index') ? 'text-sand-975 dark:text-sand-100 bg-sand-100/80 dark:bg-sand-900 font-semibold' : 'text-sand-600 dark:text-sand-500 hover:text-sand-975 dark:hover:text-sand-100 hover:bg-sand-100/40 dark:hover:bg-sand-925' }}">
                    <flux:icon name="map" class="w-3.5 h-3.5 shrink-0 text-sand-600 dark:text-sand-500" />
                    <span>Peta Sebaran</span>
                </a>

                <!-- Dropdown Manajemen Khusus Admin -->
                @auth
                    @if (Auth::user()->isAdmin())
                        <div class="relative" id="adminNavDropdownContainer">
                            <button
                                type="button"
                                id="adminNavDropdownBtn"
                                aria-expanded="false"
                                class="px-3 py-1.5 rounded-md transition-colors flex items-center space-x-1.5 cursor-pointer {{ request()->routeIs('admin.*') ? 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 font-semibold' : 'text-sand-600 dark:text-sand-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-sand-100/40 dark:hover:bg-sand-925' }}"
                            >
                                <flux:icon name="shield-check" class="w-3.5 h-3.5 shrink-0 text-amber-500" />
                                <span>Manajemen</span>
                                <flux:icon name="chevron-down" id="adminNavChevron" class="w-3 h-3 text-amber-500 transition-transform duration-200" />
                            </button>

                            <div
                                id="adminNavDropdownMenu"
                                class="hidden absolute left-0 mt-1.5 w-52 rounded-xl bg-white dark:bg-sand-950 border border-sand-100 dark:border-sand-850 shadow-lg py-1 z-50 transition-all font-mono"
                            >
                                <a href="{{ route('admin.reports.index') }}"
                                   class="flex items-center space-x-2.5 px-3 py-2 text-xs text-sand-975 dark:text-sand-100 hover:bg-sand-50 dark:hover:bg-sand-925 transition {{ request()->routeIs('admin.reports.*') ? 'bg-sand-50 dark:bg-sand-925 font-bold text-amber-600 dark:text-amber-400' : '' }}">
                                    <flux:icon name="clipboard-document-list" class="w-4 h-4 text-amber-500 shrink-0" />
                                    <span>Manajemen Laporan</span>
                                </a>
                                <a href="{{ route('admin.users.index') }}"
                                   class="flex items-center space-x-2.5 px-3 py-2 text-xs text-sand-975 dark:text-sand-100 hover:bg-sand-50 dark:hover:bg-sand-925 transition {{ request()->routeIs('admin.users.*') ? 'bg-sand-50 dark:bg-sand-925 font-bold text-amber-600 dark:text-amber-400' : '' }}">
                                    <flux:icon name="users" class="w-4 h-4 text-amber-500 shrink-0" />
                                    <span>Manajemen User</span>
                                </a>
                            </div>
                        </div>
                    @endif
                @endauth

                @if (request()->routeIs('home'))
                    <a href="#cara-kerja-fitur" class="px-3 py-1.5 rounded-md transition-colors text-sand-600 dark:text-sand-500 hover:text-sand-975 dark:hover:text-sand-100 hover:bg-sand-100/40 dark:hover:bg-sand-925 hidden xl:inline-block">
                        Cara Kerja &amp; Fitur
                    </a>
                @endif
            </nav>
        </div>

        <!-- Action & Auth Navigation -->
        <div class="flex items-center space-x-2.5 sm:space-x-3 font-mono text-xs">
            <!-- Tombol Pengalih Tema (Light / Dark) -->
            <button
                id="theme-toggle"
                type="button"
                aria-label="Ganti tema warna"
                title="Ganti tema warna"
                class="w-9 h-9 rounded-lg border border-sand-100 dark:border-sand-850 bg-white dark:bg-sand-950 text-sand-975 dark:text-sand-100 hover:bg-sand-50 dark:hover:bg-sand-925 transition-all active:scale-[0.98] cursor-pointer inline-flex items-center justify-center shrink-0"
            >
                <flux:icon id="theme-toggle-light-icon" name="sun" class="w-4 h-4 hidden" />
                <flux:icon id="theme-toggle-dark-icon" name="moon" class="w-4 h-4 hidden" />
            </button>

            <div class="hidden md:block shrink-0">
                <x-report-cta size="sm" />
            </div>

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
                        class="relative w-9 h-9 rounded-lg border border-sand-100 dark:border-sand-850 bg-white dark:bg-sand-950 text-sand-975 dark:text-sand-100 hover:bg-sand-50 dark:hover:bg-sand-925 transition-all active:scale-[0.98] cursor-pointer flex items-center justify-center shrink-0"
                    >
                        <flux:icon name="bell" class="w-4 h-4" />

                        <!-- Notification Count Badge -->
                        <span id="notificationBadge" class="{{ $unreadCount > 0 ? '' : 'hidden' }} absolute -top-1.5 -right-1.5 flex h-4 min-w-4 px-1 items-center justify-center rounded-full bg-danger text-[9px] font-bold text-white shadow-xs font-mono">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="notificationMenu" class="hidden absolute right-0 mt-2 w-80 sm:w-92 max-w-[92vw] bg-white dark:bg-sand-975 border border-sand-100 dark:border-sand-900 rounded-xl shadow-2xl z-50 overflow-hidden font-sans text-xs overscroll-contain">
                        <div class="px-4 py-3 border-b border-sand-100 dark:border-sand-900 flex items-center justify-between bg-sand-25 dark:bg-sand-950">
                            <div class="flex items-center space-x-2">
                                <span class="font-semibold text-sand-975 dark:text-sand-100">Notifikasi</span>
                                <span id="notificationUnreadPill" class="{{ $unreadCount > 0 ? '' : 'hidden' }} px-1.5 py-0.5 rounded-full text-[10px] font-mono bg-danger-tint text-danger dark:bg-danger-deep dark:text-danger-soft">
                                    <span id="notificationUnreadCount">{{ $unreadCount }}</span> baru
                                </span>
                            </div>

                            <div class="flex items-center space-x-2.5 text-[11px] font-mono">
                                <button type="button" id="markAllReadBtn" title="Tandai semua notifikasi telah dibaca" class="text-sand-600 hover:text-sand-975 dark:text-sand-500 dark:hover:text-sand-100 transition-colors {{ $unreadCount > 0 ? '' : 'hidden' }}">
                                    Tandai dibaca
                                </button>
                                <button type="button" id="clearAllNotificationsBtn" title="Bersihkan semua riwayat notifikasi" class="text-sand-600 hover:text-danger dark:text-sand-500 dark:hover:text-danger-soft transition-colors {{ $recentNotifications->isNotEmpty() ? '' : 'hidden' }}">
                                    Hapus semua
                                </button>
                            </div>
                        </div>

                        <!-- Notification List Items -->
                        <div id="notificationList" class="max-h-80 overflow-y-auto overscroll-contain divide-y divide-sand-100/70 dark:divide-sand-900/70">
                            @forelse ($recentNotifications as $notification)
                                @php
                                    $data = $notification->data;
                                    $isUnread = $notification->unread();
                                    $type = $data['type'] ?? 'reply';
                                    $isAi = $data['is_ai'] ?? false;
                                @endphp
                                <div id="notif-item-{{ $notification->id }}"
                                     data-unread="{{ $isUnread ? '1' : '0' }}"
                                     class="group relative flex items-start justify-between p-3 hover:bg-sand-50 dark:hover:bg-sand-925 transition duration-150 {{ $isUnread ? 'bg-sand-25 dark:bg-sand-950' : '' }}">
                                    <a href="{{ route('notifications.markAsRead', ['id' => $notification->id, 'redirect' => 1]) }}"
                                       class="flex items-start space-x-2.5 flex-1 min-w-0 pr-2">
                                        <div class="w-6 h-6 rounded-full shrink-0 flex items-center justify-center text-[10px] {{ $isAi ? 'overflow-hidden' : (in_array($type, ['mention', 'post_mention']) ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300') }}">
                                            @if ($isAi)
                                                <img src="{{ asset('android-chrome-192x192.png') }}" alt="SIRA AI" class="w-6 h-6 rounded-full object-cover shrink-0">
                                            @elseif (in_array($type, ['mention', 'post_mention']))
                                                <span class="font-bold text-[11px]">@</span>
                                            @else
                                                <flux:icon name="chat-bubble-left" class="w-3 h-3 text-emerald-800 dark:text-emerald-300" />
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0 space-y-0.5">
                                            <p class="text-xs text-sand-975 dark:text-sand-100 leading-snug">
                                                {{ $data['message'] ?? 'Pembaruan pada laporan' }}
                                            </p>
                                            @if (!empty($data['snippet']))
                                                <p class="text-[11px] text-sand-600 dark:text-sand-500 truncate italic">
                                                    "{{ $data['snippet'] }}"
                                                </p>
                                            @endif
                                            <div class="flex items-center justify-between text-[10px] font-mono text-sand-500 pt-0.5">
                                                <span class="truncate max-w-[150px] sm:max-w-[180px]">{{ $data['report_title'] ?? '' }}</span>
                                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        @if ($isUnread)
                                            <span class="w-1.5 h-1.5 rounded-full bg-danger shrink-0 mt-1.5 notif-unread-dot"></span>
                                        @endif
                                    </a>

                                    <!-- Tombol Hapus per Notifikasi -->
                                    <button type="button"
                                            onclick="deleteSingleNotification(event, '{{ $notification->id }}')"
                                            title="Hapus notifikasi ini"
                                            class="opacity-0 group-hover:opacity-100 focus:opacity-100 p-1 text-sand-500 hover:text-danger dark:text-sand-600 dark:hover:text-danger-soft rounded transition-all shrink-0">
                                        <flux:icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            @empty
                                <div id="notificationEmptyNotice" class="p-6 text-center text-xs text-sand-600 dark:text-sand-500 font-mono">
                                    Belum ada notifikasi baru.
                                </div>
                            @endforelse
                            <div id="notificationEmptyNoticeFallback" class="hidden p-6 text-center text-xs text-sand-600 dark:text-sand-500 font-mono">
                                Belum ada notifikasi baru.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-2 sm:space-x-2.5 pl-2 sm:pl-3 border-l border-sand-100 dark:border-sand-850 h-9">
                    <a href="{{ route('account.index') }}"
                       class="inline-flex items-center space-x-1.5 px-2 py-1 rounded-md {{ request()->routeIs('account.*') ? 'bg-sand-100/80 dark:bg-sand-900 text-sand-975 dark:text-sand-100 font-semibold' : 'text-sand-600 dark:text-sand-500 hover:text-sand-975 dark:hover:text-sand-100 hover:bg-sand-100/40 dark:hover:bg-sand-925' }} text-[11px] transition-colors group"
                       title="Menu Akun & Pengaturan Profil">
                        @if (Auth::user()->avatar_url)
                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-4 h-4 rounded-full object-cover shrink-0">
                        @else
                            <flux:icon name="user-circle" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform" />
                        @endif
                        <span>@<span>{{ Auth::user()->username }}</span></span>
                        <x-verified-badge :user="Auth::user()" size="xs" />
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline-flex items-center">
                        @csrf
                        <button type="submit" title="Keluar" class="text-sand-600 hover:text-danger dark:hover:text-danger-soft transition-colors cursor-pointer text-[11px]">
                            [Keluar]
                        </button>
                    </form>
                </div>

                <!-- Backdrop Overlay Notifikasi (dipindah ke <body> oleh script di bawah) -->
                <div id="notificationBackdrop"
                     class="fixed inset-0 top-16 bg-black/40 backdrop-blur-[2px] z-40 transition-opacity duration-300 opacity-0 pointer-events-none"
                     aria-hidden="true"></div>

                <!-- Script Dropdown Notifikasi, Delete, & Clear All -->
                <script>
                    (function () {
                        const btn = document.getElementById('notificationBellBtn');
                        const menu = document.getElementById('notificationMenu');
                        const container = document.getElementById('notificationDropdownContainer');
                        const backdrop = document.getElementById('notificationBackdrop');
                        const markAllBtn = document.getElementById('markAllReadBtn');
                        const clearAllBtn = document.getElementById('clearAllNotificationsBtn');
                        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

                        if (btn && menu) {
                            // backdrop-blur pada <header> menjadikannya containing block untuk elemen fixed
                            if (backdrop) document.body.appendChild(backdrop);

                            function setNotificationOpen(open) {
                                menu.classList.toggle('hidden', !open);
                                if (backdrop) {
                                    backdrop.classList.toggle('opacity-0', !open);
                                    backdrop.classList.toggle('pointer-events-none', !open);
                                }
                            }

                            btn.addEventListener('click', function (e) {
                                e.stopPropagation();
                                setNotificationOpen(menu.classList.contains('hidden'));
                            });

                            document.addEventListener('click', function (e) {
                                if (container && !container.contains(e.target)) {
                                    setNotificationOpen(false);
                                }
                            });

                            // Mencegah scroll chaining ke halaman saat menu notifikasi di-scroll hingga mentok (overscroll)
                            menu.addEventListener('wheel', function (e) {
                                const list = document.getElementById('notificationList');
                                if (!list) return;

                                if (list.contains(e.target)) {
                                    const isScrollingDown = e.deltaY > 0;
                                    const isScrollingUp = e.deltaY < 0;
                                    const reachedBottom = (list.scrollTop + list.clientHeight) >= (list.scrollHeight - 1);
                                    const reachedTop = list.scrollTop <= 0;

                                    if ((isScrollingDown && reachedBottom) || (isScrollingUp && reachedTop)) {
                                        e.preventDefault();
                                    }
                                } else {
                                    // Cegah scroll halaman jika kursor berada di header menu notifikasi
                                    e.preventDefault();
                                }
                            }, { passive: false });
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
                                            el.classList.remove('bg-sand-25', 'dark:bg-sand-950');
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
                            bell.classList.add('ring-2', 'ring-danger', 'scale-110');
                            setTimeout(() => {
                                bell.classList.remove('ring-2', 'ring-danger', 'scale-110');
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
                            const iconSvg = isAi
                                ? '<img src="{{ asset('android-chrome-192x192.png') }}" alt="SIRA AI" class="w-6 h-6 rounded-full object-cover shrink-0">'
                                : ((type === 'mention' || type === 'post_mention') ? '<span class="font-bold text-[11px]">@</span>' : '<svg class="w-3 h-3" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M2.5 3.5A1.5 1.5 0 0 1 4 2h8a1.5 1.5 0 0 1 1.5 1.5v6A1.5 1.5 0 0 1 12 11H5.5L2.5 13.5V3.5z"/></svg>');
                            const iconCls = isAi
                                ? 'overflow-hidden'
                                : ((type === 'mention' || type === 'post_mention') ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300');

                            const div = document.createElement('div');
                            div.id = 'notif-item-' + item.id;
                            div.setAttribute('data-unread', '1');
                            div.className = 'group relative flex items-start justify-between p-3 hover:bg-sand-50 dark:hover:bg-sand-925 transition duration-200 bg-sand-25 dark:bg-sand-950';

                            div.innerHTML = `
                                <a href="/notifications/${item.id}/read?redirect=1" class="flex items-start space-x-2.5 flex-1 min-w-0 pr-2">
                                    <div class="w-6 h-6 rounded-full shrink-0 flex items-center justify-center text-[10px] ${iconCls}">
                                        ${iconSvg}
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-0.5">
                                        <p class="text-xs text-sand-975 dark:text-sand-100 leading-snug font-medium">
                                            ${escapeHtml(d.message || 'Pembaruan pada laporan')}
                                        </p>
                                        ${d.snippet ? `<p class="text-[11px] text-sand-600 dark:text-sand-500 truncate italic">"${escapeHtml(d.snippet)}"</p>` : ''}
                                        <div class="flex items-center justify-between text-[10px] font-mono text-sand-500 pt-0.5">
                                            <span class="truncate max-w-[150px] sm:max-w-[180px]">${escapeHtml(d.report_title || '')}</span>
                                            <span>Baru saja</span>
                                        </div>
                                    </div>
                                    <span class="w-1.5 h-1.5 rounded-full bg-danger shrink-0 mt-1.5 notif-unread-dot"></span>
                                </a>
                                <button type="button"
                                        onclick="deleteSingleNotification(event, '${item.id}')"
                                        title="Hapus notifikasi ini"
                                        class="opacity-0 group-hover:opacity-100 focus:opacity-100 p-1 text-sand-500 hover:text-danger dark:text-sand-600 dark:hover:text-danger-soft rounded transition-all shrink-0">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75">
                                        <path d="M3 4.5h10M6 7v4.5M10 7v4.5M4.5 4.5V13a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1V4.5M5.5 4.5V3a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            `;

                            list.prepend(div);
                        }

                        // Menampilkan Toast notifikasi realtime melayang di pojok kanan bawah
                        function showRealtimeNotificationToast(item) {
                            if (!item || !item.id) return;

                            // Cegah toast yang sama muncul berkali-kali setiap pindah halaman
                            try {
                                const shownToasts = new Set(JSON.parse(sessionStorage.getItem('sira_shown_toasts') || '[]'));
                                if (shownToasts.has(String(item.id))) {
                                    return;
                                }
                                shownToasts.add(String(item.id));
                                sessionStorage.setItem('sira_shown_toasts', JSON.stringify([...shownToasts]));
                            } catch (e) {}

                            const container = document.getElementById('realtimeNotificationToastContainer');
                            if (!container) return;

                            const d = item.data || {};
                            const isAi = d.is_ai || false;
                            const type = d.type || 'reply';
                            const toastIconSvg = isAi
                                ? '<img src="{{ asset('android-chrome-192x192.png') }}" alt="SIRA AI" class="w-7 h-7 rounded-full object-cover shrink-0">'
                                : ((type === 'mention' || type === 'post_mention') ? '<span class="font-bold text-xs">@</span>' : '<svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M2.5 3.5A1.5 1.5 0 0 1 4 2h8a1.5 1.5 0 0 1 1.5 1.5v6A1.5 1.5 0 0 1 12 11H5.5L2.5 13.5V3.5z"/></svg>');

                            const toast = document.createElement('div');
                            toast.className = 'pointer-events-auto flex items-start space-x-3 p-3.5 rounded-xl bg-white dark:bg-sand-975 border border-sand-100 dark:border-sand-850 shadow-xl text-xs transform translate-y-4 opacity-0 transition-all duration-300 max-w-sm';

                            toast.innerHTML = `
                                <div class="w-7 h-7 rounded-full shrink-0 flex items-center justify-center text-xs font-bold ${isAi ? 'overflow-hidden' : ((type === 'mention' || type === 'post_mention') ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300')}">
                                    ${toastIconSvg}
                                </div>
                                <div class="flex-1 min-w-0 space-y-0.5">
                                    <div class="flex items-center justify-between">
                                        <span class="font-mono text-[10px] uppercase font-bold tracking-wider text-danger">Notifikasi Baru</span>
                                        <button type="button" class="text-sand-500 hover:text-sand-975 dark:hover:text-white toast-close-btn">&times;</button>
                                    </div>
                                    <a href="/notifications/${item.id}/read?redirect=1" class="block font-medium text-sand-975 dark:text-sand-100 hover:underline leading-snug">
                                        ${escapeHtml(d.message || 'Pembaruan baru')}
                                    </a>
                                    ${d.snippet ? `<p class="text-[11px] text-sand-600 dark:text-sand-500 truncate italic">"${escapeHtml(d.snippet)}"</p>` : ''}
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
                <div class="hidden md:flex items-center space-x-1.5 sm:space-x-2 pl-2 sm:pl-3 border-l border-sand-100 dark:border-sand-850 h-9">
                    <a href="{{ route('login') }}" class="h-9 inline-flex items-center justify-center text-slate-950 font-bold bg-amber-400 hover:bg-amber-500 border border-amber-500/60 px-3 rounded-lg shadow-xs transition-all">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="h-9 inline-flex items-center justify-center text-sand-975 dark:text-sand-975 bg-sand-100 hover:bg-sand-200 dark:bg-sand-100 dark:hover:bg-white px-3 rounded-lg font-medium transition-colors">
                        Daftar
                    </a>
                </div>
            @endauth

            <!-- Tombol Toggle Menu Mobile (Layar kecil < md) -->
            <button
                id="mobileMenuBtn"
                type="button"
                aria-label="Menu navigasi utama"
                aria-expanded="false"
                class="md:hidden relative w-9 h-9 rounded-lg border border-sand-100 dark:border-sand-850 bg-white dark:bg-sand-950 text-sand-975 dark:text-sand-100 hover:bg-sand-50 dark:hover:bg-sand-925 active:scale-95 transition-all cursor-pointer flex items-center justify-center shrink-0"
            >
                <flux:icon id="mobileMenuOpenIcon" name="bars-3" class="w-4 h-4 transition-all duration-200" />
                <flux:icon id="mobileMenuCloseIcon" name="x-mark" class="w-4 h-4 transition-all duration-200 hidden" />
            </button>
        </div>
    </div>

    <!-- Backdrop Overlay Mobile (Mencegah pergeseran halaman & klik luar untuk tutup) -->
    <div id="mobileMenuBackdrop"
         class="fixed inset-0 top-16 bg-black/40 backdrop-blur-[2px] z-40 transition-opacity duration-300 opacity-0 pointer-events-none md:hidden"
         aria-hidden="true"></div>

    <!-- Panel Menu Navigasi Mobile (Floating Overlay - Tidak menggeser layout halaman) -->
    <div id="mobileMenu"
         class="absolute top-full left-0 right-0 w-full z-50 md:hidden border-b border-sand-100 dark:border-sand-900 bg-sand-25/98 dark:bg-sand-975/98 backdrop-blur-xl shadow-xl px-4 py-4 space-y-3 font-mono text-xs transition-all duration-300 ease-out transform -translate-y-2 opacity-0 pointer-events-none max-h-[calc(100dvh-4rem)] overflow-y-auto overscroll-contain">
        <div class="space-y-1.5">
            <!-- Accordion Kategori Mobile: Laporan -->
            <div class="rounded-md overflow-hidden border border-sand-100 dark:border-sand-900 bg-white/40 dark:bg-sand-950/40">
                <button type="button" id="mobileReportsAccordionBtn"
                    class="w-full flex items-center justify-between px-3 py-2.5 text-sand-975 dark:text-sand-100 font-semibold transition cursor-pointer hover:bg-sand-100/40 dark:hover:bg-sand-925">
                    <span class="flex items-center space-x-2">
                        <flux:icon name="document-text" class="w-3.5 h-3.5 text-sand-600 dark:text-sand-500" />
                        <span>Laporan</span>
                    </span>
                    <flux:icon name="chevron-down" id="mobileReportsChevron"
                        class="w-3.5 h-3.5 text-sand-600 dark:text-sand-500 transition-transform duration-200 {{ request()->routeIs('reports.*') ? 'rotate-180' : '' }}" />
                </button>

                <!-- Sub-menu Laporan -->
                <div id="mobileReportsSubmenu" class="px-2 pb-2 pt-0.5 space-y-1 border-t border-sand-100/60 dark:border-sand-900/60 {{ request()->routeIs('reports.*') ? '' : 'hidden' }}">
                    <a href="{{ route('reports.index') }}"
                       class="flex items-center justify-between px-3 py-2 rounded-md text-sand-975 dark:text-sand-100 {{ request()->routeIs('reports.index') && !request('my_reports') ? 'bg-sand-100/80 dark:bg-sand-900 font-semibold' : 'hover:bg-sand-100/30 dark:hover:bg-sand-925' }} transition">
                        <span class="flex items-center space-x-2">
                            <flux:icon name="queue-list" class="w-3.5 h-3.5 text-sand-600 dark:text-sand-500" />
                            <span>Semua Laporan</span>
                        </span>
                        <span class="text-[10px] text-sand-600">&rarr;</span>
                    </a>
                    @auth
                        <a href="{{ route('reports.index', ['my_reports' => 1]) }}"
                           class="flex items-center justify-between px-3 py-2 rounded-md text-sand-975 dark:text-sand-100 {{ request()->routeIs('reports.index') && request('my_reports') ? 'bg-sand-100/80 dark:bg-sand-900 font-semibold text-emerald-600 dark:text-emerald-400' : 'hover:bg-sand-100/30 dark:hover:bg-sand-925' }} transition">
                            <span class="flex items-center space-x-2">
                                <flux:icon name="user" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                                <span class="text-emerald-700 dark:text-emerald-400 font-medium">Laporan Saya</span>
                            </span>
                            <span class="text-[10px] text-sand-600">&rarr;</span>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Tautan Peta Sebaran Masalah -->
            <a href="{{ route('heatmap.index') }}"
               class="flex items-center justify-between px-3 py-2.5 rounded-md border border-sand-100 dark:border-sand-900 bg-white/40 dark:bg-sand-950/40 text-sand-975 dark:text-sand-100 {{ request()->routeIs('heatmap.index') ? 'bg-sand-100/80 dark:bg-sand-900 font-semibold' : 'hover:bg-sand-100/40 dark:hover:bg-sand-925' }} transition">
                <span class="flex items-center space-x-2">
                    <flux:icon name="map" class="w-3.5 h-3.5 text-sand-600 dark:text-sand-500" />
                    <span>Peta Sebaran Masalah</span>
                </span>
                <span class="text-[11px] text-sand-600">&rarr;</span>
            </a>

            <!-- Accordion Kategori Mobile: Manajemen Admin -->
            @auth
                @if (Auth::user()->isAdmin())
                    <div class="rounded-md overflow-hidden border border-sand-100 dark:border-sand-900 bg-white/40 dark:bg-sand-950/40">
                        <button type="button" id="mobileAdminAccordionBtn"
                            class="w-full flex items-center justify-between px-3 py-2.5 text-amber-600 dark:text-amber-400 font-semibold transition cursor-pointer hover:bg-sand-100/40 dark:hover:bg-sand-925">
                            <span class="flex items-center space-x-2">
                                <flux:icon name="shield-check" class="w-3.5 h-3.5 text-amber-500" />
                                <span>Manajemen Admin</span>
                            </span>
                            <flux:icon name="chevron-down" id="mobileAdminChevron"
                                class="w-3.5 h-3.5 text-amber-500 transition-transform duration-200 {{ request()->routeIs('admin.*') ? 'rotate-180' : '' }}" />
                        </button>

                        <!-- Sub-menu Manajemen Admin -->
                        <div id="mobileAdminSubmenu" class="px-2 pb-2 pt-0.5 space-y-1 border-t border-sand-100/60 dark:border-sand-900/60 {{ request()->routeIs('admin.*') ? '' : 'hidden' }}">
                            <a href="{{ route('admin.reports.index') }}"
                               class="flex items-center justify-between px-3 py-2 rounded-md text-sand-975 dark:text-sand-100 {{ request()->routeIs('admin.reports.*') ? 'bg-sand-100/80 dark:bg-sand-900 font-semibold text-amber-600 dark:text-amber-400' : 'hover:bg-sand-100/30 dark:hover:bg-sand-925' }} transition">
                                <span class="flex items-center space-x-2">
                                    <flux:icon name="clipboard-document-list" class="w-3.5 h-3.5 text-amber-500" />
                                    <span>Manajemen Laporan</span>
                                </span>
                                <span class="text-[10px] text-sand-600">&rarr;</span>
                            </a>
                            <a href="{{ route('admin.users.index') }}"
                               class="flex items-center justify-between px-3 py-2 rounded-md text-sand-975 dark:text-sand-100 {{ request()->routeIs('admin.users.*') ? 'bg-sand-100/80 dark:bg-sand-900 font-semibold text-amber-600 dark:text-amber-400' : 'hover:bg-sand-100/30 dark:hover:bg-sand-925' }} transition">
                                <span class="flex items-center space-x-2">
                                    <flux:icon name="users" class="w-3.5 h-3.5 text-amber-500" />
                                    <span>Manajemen User</span>
                                </span>
                                <span class="text-[10px] text-sand-600">&rarr;</span>
                            </a>
                        </div>
                    </div>
                @endif
            @endauth

            @if (request()->routeIs('home'))
                <a href="#cara-kerja-fitur" class="flex items-center justify-between px-3 py-2.5 rounded-md text-sand-600 dark:text-sand-500 hover:text-sand-975 dark:hover:text-sand-100 hover:bg-sand-100/40 dark:hover:bg-sand-925 transition">
                    <span>Cara Kerja &amp; Fitur</span>
                    <span class="text-[11px] text-sand-600">&rarr;</span>
                </a>
            @endif
        </div>

        <div class="pt-2 border-t border-sand-100 dark:border-sand-900 space-y-2">
            <x-report-cta class="w-full" />

            @auth
                <div class="space-y-1.5 pt-1">
                    <a href="{{ route('account.index') }}"
                       class="flex items-center justify-between px-3 py-2 rounded-md border border-sand-100 dark:border-sand-900 bg-white/40 dark:bg-sand-950/40 text-sand-975 dark:text-sand-100 {{ request()->routeIs('account.*') ? 'bg-sand-100/80 dark:bg-sand-900 font-semibold' : 'hover:bg-sand-100/30 dark:hover:bg-sand-925' }} transition">
                        <span class="flex items-center space-x-2">
                            <flux:icon name="user-circle" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                            <span>Menu Akun (@<span>{{ Auth::user()->username }}</span>)</span>
                            <x-verified-badge :user="Auth::user()" size="xs" />
                        </span>
                        <span class="text-[10px] text-sand-600">&rarr;</span>
                    </a>
                </div>

                <div class="flex items-center justify-between px-2 pt-1 text-sand-600 dark:text-sand-500 text-xs">
                    <span class="inline-flex items-center space-x-1 truncate max-w-[200px]">
                        <span>Masuk: <strong class="text-sand-975 dark:text-sand-100 truncate">{{ Auth::user()->name }}</strong></span>
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-danger dark:text-danger-soft hover:underline font-medium">
                            [Keluar]
                        </button>
                    </form>
                </div>
            @else
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <a href="{{ route('login') }}" class="py-2.5 text-center rounded-xl bg-amber-400 hover:bg-amber-500 border border-amber-500/60 text-slate-950 font-bold shadow-xs transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="py-2.5 text-center rounded-xl bg-sand-100 dark:bg-sand-850 text-sand-975 dark:text-sand-100 hover:bg-sand-200 dark:hover:bg-sand-800 font-medium">
                        Daftar
                    </a>
                </div>
            @endauth
        </div>
    </div>

    <script>
        (function () {
            const mobileBtn = document.getElementById('mobileMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileBackdrop = document.getElementById('mobileMenuBackdrop');
            const openIcon = document.getElementById('mobileMenuOpenIcon');
            const closeIcon = document.getElementById('mobileMenuCloseIcon');

            if (!mobileBtn || !mobileMenu) return;

            // backdrop-blur pada <header> menjadikannya containing block untuk elemen fixed,
            // sehingga backdrop harus dipindah ke <body> agar menutupi seluruh layar
            if (mobileBackdrop) document.body.appendChild(mobileBackdrop);

            let isOpen = false;

            function setMenuState(open) {
                isOpen = open;
                mobileBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

                if (isOpen) {
                    // Animasi buka panel overlay ke bawah secara halus
                    mobileMenu.classList.remove('-translate-y-2', 'opacity-0', 'pointer-events-none');
                    mobileMenu.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');

                    if (mobileBackdrop) {
                        mobileBackdrop.classList.remove('opacity-0', 'pointer-events-none');
                        mobileBackdrop.classList.add('opacity-100', 'pointer-events-auto');
                    }

                    if (openIcon && closeIcon) {
                        openIcon.classList.add('hidden');
                        closeIcon.classList.remove('hidden');
                    }
                } else {
                    // Animasi tutup panel overlay ke atas secara halus
                    mobileMenu.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
                    mobileMenu.classList.add('-translate-y-2', 'opacity-0', 'pointer-events-none');

                    if (mobileBackdrop) {
                        mobileBackdrop.classList.remove('opacity-100', 'pointer-events-auto');
                        mobileBackdrop.classList.add('opacity-0', 'pointer-events-none');
                    }

                    if (openIcon && closeIcon) {
                        closeIcon.classList.add('hidden');
                        openIcon.classList.remove('hidden');
                    }
                }
            }

            mobileBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                setMenuState(!isOpen);
            });

            if (mobileBackdrop) {
                mobileBackdrop.addEventListener('click', function () {
                    setMenuState(false);
                });

                mobileBackdrop.addEventListener('touchmove', function (e) {
                    e.preventDefault();
                }, { passive: false });

                mobileBackdrop.addEventListener('wheel', function (e) {
                    e.preventDefault();
                }, { passive: false });
            }

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && isOpen) {
                    setMenuState(false);
                }
            });

            mobileMenu.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    setMenuState(false);
                });
            });
        })();

        // Handler Dropdown Navigasi Desktop (Laporan & Manajemen Admin)
        (function () {
            function setupNavDropdown(btnId, menuId, containerId, chevronId) {
                const btn = document.getElementById(btnId);
                const menu = document.getElementById(menuId);
                const container = document.getElementById(containerId);
                const chevron = document.getElementById(chevronId);
                if (!btn || !menu) return;

                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const willOpen = menu.classList.contains('hidden');

                    // Tutup dropdown lain yang sedang terbuka
                    document.querySelectorAll('#reportsNavDropdownMenu, #adminNavDropdownMenu').forEach(function (m) {
                        m.classList.add('hidden');
                    });
                    document.querySelectorAll('#reportsNavChevron, #adminNavChevron').forEach(function (c) {
                        c.classList.remove('rotate-180');
                    });

                    if (willOpen) {
                        menu.classList.remove('hidden');
                        btn.setAttribute('aria-expanded', 'true');
                        if (chevron) chevron.classList.add('rotate-180');
                    } else {
                        btn.setAttribute('aria-expanded', 'false');
                    }
                });

                document.addEventListener('click', function (e) {
                    if (container && !container.contains(e.target)) {
                        menu.classList.add('hidden');
                        btn.setAttribute('aria-expanded', 'false');
                        if (chevron) chevron.classList.remove('rotate-180');
                    }
                });

                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        menu.classList.add('hidden');
                        btn.setAttribute('aria-expanded', 'false');
                        if (chevron) chevron.classList.remove('rotate-180');
                    }
                });

                menu.querySelectorAll('a').forEach(function (link) {
                    link.addEventListener('click', function () {
                        menu.classList.add('hidden');
                        btn.setAttribute('aria-expanded', 'false');
                        if (chevron) chevron.classList.remove('rotate-180');
                    });
                });
            }

            setupNavDropdown('reportsNavDropdownBtn', 'reportsNavDropdownMenu', 'reportsNavDropdownContainer', 'reportsNavChevron');
            setupNavDropdown('adminNavDropdownBtn', 'adminNavDropdownMenu', 'adminNavDropdownContainer', 'adminNavChevron');
        })();

        // Handler Accordion Kategori Menu Mobile
        (function () {
            function setupMobileAccordion(btnId, submenuId, chevronId) {
                const btn = document.getElementById(btnId);
                const submenu = document.getElementById(submenuId);
                const chevron = document.getElementById(chevronId);
                if (!btn || !submenu) return;

                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const isHidden = submenu.classList.toggle('hidden');
                    if (chevron) {
                        chevron.classList.toggle('rotate-180', !isHidden);
                    }
                });
            }

            setupMobileAccordion('mobileReportsAccordionBtn', 'mobileReportsSubmenu', 'mobileReportsChevron');
            setupMobileAccordion('mobileAdminAccordionBtn', 'mobileAdminSubmenu', 'mobileAdminChevron');
        })();
    </script>
</header>
