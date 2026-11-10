@extends('layouts.app')

@section('title', 'Menu Akun — SIRA')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:py-10">
    <!-- Breadcrumb & Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-2 text-xs text-sand-600 dark:text-sand-500 mb-2">
            <a href="{{ route('home') }}" class="hover:text-sand-975 dark:hover:text-sand-100">Beranda</a>
            <span>/</span>
            <span class="text-sand-975 dark:text-sand-100 font-semibold">Menu Akun</span>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-sans text-2xl sm:text-3xl font-bold tracking-tight text-sand-975 dark:text-sand-100">
                    Pengaturan Akun &amp; Keamanan
                </h1>
                <p class="text-xs text-sand-600 dark:text-sand-500 mt-1 font-sans">
                    Kelola profil identitas Anda, integrasi login Google, dan kredensial kata sandi akun SIRA.
                </p>
            </div>

            <a href="{{ route('reports.index', ['my_reports' => 1]) }}"
               class="inline-flex items-center space-x-2 text-xs px-3.5 py-2 rounded-md border border-sand-100 dark:border-sand-850 bg-white dark:bg-sand-950 text-sand-975 dark:text-sand-100 hover:bg-sand-50 dark:hover:bg-sand-925 transition shrink-0 self-start sm:self-auto">
                <flux:icon name="document-text" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                <span>Lihat Laporan Saya &rarr;</span>
            </a>
        </div>
    </div>

    <div class="space-y-6">
        <!-- 1. BENTO CARD: Profil Pengguna -->
        <div class="bg-white dark:bg-sand-975 border border-sand-100 dark:border-sand-900 rounded-xl p-6 sm:p-8">
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-sand-100 dark:border-sand-900">
                <div class="flex items-center space-x-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-sand-975 dark:text-sand-100">
                        Profil Pengguna
                    </h2>
                </div>
                <span class="text-xs text-sand-600 dark:text-sand-500">
                    Terdaftar sejak {{ $user->created_at?->translatedFormat('d F Y') ?? '-' }}
                </span>
            </div>

            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 mb-6 p-4 rounded-md bg-sand-25 dark:bg-sand-950 border border-sand-100 dark:border-sand-900">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-14 h-14 rounded-full object-cover border border-sand-100 dark:border-sand-800 shrink-0">
                @else
                    <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 flex items-center justify-center text-lg font-bold shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif

                <div class="space-y-1">
                    <div class="flex items-center space-x-2">
                        <span class="text-base font-bold text-sand-975 dark:text-sand-100">{{ $user->name }}</span>
                        <x-verified-badge :user="$user" size="sm" />
                    </div>
                    <div class="flex items-center space-x-2 text-xs text-sand-600 dark:text-sand-500">
                        <span>@<span>{{ $user->username }}</span></span>
                        @if ($user->isAdmin())
                            <span class="px-1.5 py-0.5 rounded-md bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 text-xs uppercase font-bold">Admin SIRA</span>
                        @endif
                    </div>
                </div>
            </div>

            <form action="{{ route('account.updateProfile') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs uppercase tracking-wider text-sand-600 dark:text-sand-500 mb-1.5">
                            Nama Lengkap
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-3.5 py-2.5 rounded-md border border-sand-100 dark:border-sand-850 bg-white dark:bg-sand-950 text-xs text-sand-975 dark:text-sand-100 focus:outline-none focus:border-sand-975 dark:focus:border-sand-100 transition">
                        @error('name')
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="username_disabled" class="block text-xs uppercase tracking-wider text-sand-600 dark:text-sand-500 mb-1.5">
                            Username <span class="text-xs lowercase text-sand-500">(permanen)</span>
                        </label>
                        <input type="text" id="username_disabled" value="{{ $user->username }}" disabled
                            class="w-full px-3.5 py-2.5 rounded-md border border-sand-100 dark:border-sand-850 bg-sand-50 dark:bg-sand-925 text-xs text-sand-600 dark:text-sand-500 cursor-not-allowed">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-xs uppercase tracking-wider text-sand-600 dark:text-sand-500 mb-1.5">
                        Alamat Email
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                        placeholder="contoh@domain.com"
                        class="w-full px-3.5 py-2.5 rounded-md border border-sand-100 dark:border-sand-850 bg-white dark:bg-sand-950 text-xs text-sand-975 dark:text-sand-100 focus:outline-none focus:border-sand-975 dark:focus:border-sand-100 transition">
                    @error('email')
                        <p class="text-xs text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit"
                        class="py-2.5 px-4 btn-primary text-xs font-medium rounded-md transition">
                        Simpan Perubahan Profil
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. BENTO CARD: Tautkan / Lepas Akun Google (Bind & Unbind) -->
        <div class="bg-white dark:bg-sand-975 border border-sand-100 dark:border-sand-900 rounded-xl p-6 sm:p-8">
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-sand-100 dark:border-sand-900">
                <div class="flex items-center space-x-3">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-sand-975 dark:text-sand-100">
                        Integrasi Akun Google
                    </h2>
                </div>

                @if ($user->isGoogleBound())
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-success-tint text-success dark:bg-success-deep dark:text-success-soft">
                        ✓ Terhubung
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sand-50 text-sand-600 dark:bg-sand-925 dark:text-sand-500">
                        Belum Ditautkan
                    </span>
                @endif
            </div>

            @if ($user->isGoogleBound())
                <div class="space-y-4">
                    <div class="p-4 rounded-md bg-success-tint/40 dark:bg-success-deep/30 border border-success/20 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-white dark:bg-sand-975 flex items-center justify-center border border-sand-100 dark:border-sand-850 shrink-0">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-sand-975 dark:text-sand-100">
                                    Akun Google Aktif Ditautkan
                                </p>
                                <p class="text-xs text-sand-600 dark:text-sand-500">
                                    {{ $user->email ?? 'ID: '.$user->google_id }}
                                </p>
                            </div>
                        </div>

                        <!-- Form Unbind -->
                        <form action="{{ route('account.google.unbind') }}" method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin melepas tautan akun Google ini?');">
                            @csrf
                            <button type="submit"
                                class="w-full sm:w-auto px-3.5 py-2 rounded-md border border-danger/30 text-danger dark:text-danger-soft hover:bg-danger-tint dark:hover:bg-danger-deep text-xs transition">
                                Lepas Tautan
                            </button>
                        </form>
                </div>

 @if (!$user->hasPassword())
                        <div class="p-3 rounded-md bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/40 text-amber-800 dark:text-amber-300 text-xs">
                            <span class="font-bold">[CATATAN KEAMANAN]</span> Anda belum memiliki kata sandi. Sebelum melepas akun Google, buatlah kata sandi di bawah agar Anda tetap dapat login.
                        </div>
                    @endif
                </div>
            @else
                <div class="space-y-4">
 <p class="text-xs text-sand-600 dark:text-sand-500 leading-relaxed font-sans">
 Tautkan akun Google Anda untuk kemudahan masuk cepat dengan 1-klik tanpa perlu mengingat atau memasukkan kata sandi setiap saat.
 </p>

 <div class="p-3.5 rounded-md bg-danger-tint dark:bg-danger-deep border border-danger/20 text-xs text-danger dark:text-danger-soft">
 <div class="font-bold mb-1">[PERINGATAN] Reset Kata Sandi Tidak Tersedia</div>
 <p class="leading-relaxed">
 Akun Anda belum menautkan <strong>Google Account</strong>. Fitur <strong>lupa kata sandi</strong> hanya dapat digunakan oleh akun yang sudah menautkan Google Account. Tautkan akun Google Anda terlebih dahulu agar Anda dapat mereset kata sandi saat lupa.
 </p>
 </div>

 <div>
                        <a href="{{ route('auth.google.redirect') }}"
                           class="inline-flex items-center space-x-2.5 px-4 py-2.5 rounded-md border border-sand-100 dark:border-sand-850 bg-white dark:bg-sand-950 hover:bg-sand-50 dark:hover:bg-sand-925 text-xs font-medium text-sand-975 dark:text-sand-100 transition shadow-2xs">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                            </svg>
                            <span>Hubungkan Akun Google</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- 3. BENTO CARD: Keamanan & Kata Sandi -->
        <div id="password-section" class="bg-white dark:bg-sand-975 border border-sand-100 dark:border-sand-900 rounded-xl p-6 sm:p-8">
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-sand-100 dark:border-sand-900">
                <div class="flex items-center space-x-3">
                    <div class="w-2.5 h-2.5 rounded-full {{ $user->hasPassword() ? 'bg-blue-500' : 'bg-amber-500' }}"></div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-sand-975 dark:text-sand-100">
                        {{ $user->hasPassword() ? 'Ubah Kata Sandi' : 'Buat Kata Sandi Akun' }}
                    </h2>
                </div>

                <span class="text-xs text-sand-600 dark:text-sand-500">
                    {{ $user->hasPassword() ? 'Status: Sandi Aktif' : 'Status: Belum Ada Sandi' }}
                </span>
            </div>

            @if (!$user->hasPassword())
                <div class="mb-5 p-3.5 rounded-md bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/40 text-xs text-amber-900 dark:text-amber-200">
                    <div class="font-bold mb-1">Akun Didaftarkan Melalui Google</div>
                    <p class="leading-relaxed">
                        Akun Anda saat ini belum memiliki kata sandi lokal. Jika Anda ingin bisa masuk menggunakan <strong>username (@{{ $user->username }})</strong> secara manual di kemudian hari, silakan tetapkan kata sandi di bawah ini.
                    </p>
                </div>
            @endif

            <form action="{{ route('account.updatePassword') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                @if ($user->hasPassword())
                    <div>
                        <label for="current_password" class="block text-xs uppercase tracking-wider text-sand-600 dark:text-sand-500 mb-1.5">
                            Kata Sandi Saat Ini
                        </label>
                        <input type="password" id="current_password" name="current_password" required
                            placeholder="••••••••"
                            class="w-full sm:w-80 px-3.5 py-2.5 rounded-md border border-sand-100 dark:border-sand-850 bg-white dark:bg-sand-950 text-xs text-sand-975 dark:text-sand-100 focus:outline-none focus:border-sand-975 dark:focus:border-sand-100 transition">
                        @error('current_password')
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="new_password" class="block text-xs uppercase tracking-wider text-sand-600 dark:text-sand-500 mb-1.5">
                            {{ $user->hasPassword() ? 'Kata Sandi Baru' : 'Kata Sandi Baru' }}
                        </label>
                        <input type="password" id="new_password" name="password" required
                            placeholder="Minimal 6 karakter"
                            class="w-full px-3.5 py-2.5 rounded-md border border-sand-100 dark:border-sand-850 bg-white dark:bg-sand-950 text-xs text-sand-975 dark:text-sand-100 focus:outline-none focus:border-sand-975 dark:focus:border-sand-100 transition">
                        @error('password')
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new_password_confirmation" class="block text-xs uppercase tracking-wider text-sand-600 dark:text-sand-500 mb-1.5">
                            Konfirmasi Kata Sandi Baru
                        </label>
                        <input type="password" id="new_password_confirmation" name="password_confirmation" required
                            placeholder="Ulangi kata sandi baru"
                            class="w-full px-3.5 py-2.5 rounded-md border border-sand-100 dark:border-sand-850 bg-white dark:bg-sand-950 text-xs text-sand-975 dark:text-sand-100 focus:outline-none focus:border-sand-975 dark:focus:border-sand-100 transition">
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit"
                        class="py-2.5 px-4 btn-primary text-xs font-medium rounded-md transition">
                        {{ $user->hasPassword() ? 'Perbarui Kata Sandi' : 'Simpan Kata Sandi' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

