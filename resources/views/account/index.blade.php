@extends('layouts.app')

@section('title', 'Menu Akun — SIRA')

@section('content')
@php
    // Bahasa visual form yang sama dengan Buat Laporan & halaman auth
    $card = 'bg-white dark:bg-sand-975 border border-sand-200 dark:border-sand-900 rounded-2xl shadow-2xs p-4 sm:p-6 space-y-5';
    $cardTitle = 'text-base sm:text-lg font-bold tracking-tight text-sand-975 dark:text-sand-100';
    $cardSub = 'text-sm text-sand-600 dark:text-sand-500';
    $label = 'block text-sm font-semibold text-sand-800 dark:text-sand-200 mb-1.5';
    $field = 'w-full px-3.5 py-2.5 rounded-lg border border-sand-300 dark:border-sand-850 bg-white dark:bg-sand-950 text-sm text-sand-975 dark:text-sand-100 placeholder-sand-400 dark:placeholder-sand-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition';
    $fieldDisabled = 'w-full px-3.5 py-2.5 rounded-lg border border-sand-200 dark:border-sand-850 bg-sand-50 dark:bg-sand-925 text-sm text-sand-500 cursor-not-allowed';
    $error = 'text-sm text-danger dark:text-danger-soft mt-1.5';
    $submit = 'w-full sm:w-auto py-2.5 px-5 btn-primary text-sm font-semibold rounded-lg transition';
    $hasPassword = $user->hasPassword();
@endphp

<div class="max-w-3xl mx-auto py-2 sm:py-4 space-y-5 sm:space-y-6">
    <!-- Judul halaman & ringkasan profil -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div class="space-y-1">
            <span class="text-xs uppercase tracking-wider text-emerald-700 dark:text-emerald-400 font-bold">Akun Saya</span>
            <h1 class="text-[1.75rem] leading-[1.15] sm:text-4xl font-extrabold tracking-tight text-sand-975 dark:text-sand-100">
                Pengaturan Akun &amp; Keamanan
            </h1>
            <p class="text-sm sm:text-base text-sand-600 dark:text-sand-400 leading-relaxed">
                Atur profil, sambungan Google, dan kata sandi Anda.
            </p>
        </div>
        <a href="{{ route('reports.index', ['my_reports' => 1]) }}"
           class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg border border-sand-200 dark:border-sand-850 bg-white dark:bg-sand-950 text-sm font-semibold text-sand-800 dark:text-sand-200 hover:bg-sand-50 dark:hover:bg-sand-925 transition shrink-0">
            <flux:icon name="document-text" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
            <span>Laporan Saya</span>
        </a>
    </div>

    <!-- 1. Profil -->
    <section class="{{ $card }}">
        <div class="flex items-center gap-4">
            @if ($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-14 h-14 rounded-full object-cover border border-sand-200 dark:border-sand-800 shrink-0">
            @else
                <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 flex items-center justify-center text-xl font-bold shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
            <div class="min-w-0 space-y-0.5">
                <div class="flex items-center gap-1.5">
                    <span class="text-lg font-bold text-sand-975 dark:text-sand-100 truncate">{{ $user->name }}</span>
                    <x-verified-badge :user="$user" size="sm" />
                </div>
                <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-sand-600 dark:text-sand-500">
                    <span>@<span>{{ $user->username }}</span></span>
                    @if ($user->isAdmin())
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 text-xs font-semibold">
                            <flux:icon name="shield-check" class="w-3.5 h-3.5" /> Admin
                        </span>
                    @endif
                    <span class="text-xs">· bergabung {{ $user->created_at?->translatedFormat('F Y') ?? '-' }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('account.updateProfile') }}" method="POST" class="space-y-4 pt-5 border-t border-sand-200 dark:border-sand-900">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="{{ $label }}">Nama lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="{{ $field }}">
                    @error('name')
                        <p class="{{ $error }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="username_disabled" class="{{ $label }}">
                        Username <span class="font-normal text-sand-500">(tidak bisa diubah)</span>
                    </label>
                    <input type="text" id="username_disabled" value="{{ $user->username }}" disabled class="{{ $fieldDisabled }}">
                </div>
            </div>

            <div>
                <label for="email" class="{{ $label }}">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                       placeholder="contoh@domain.com" class="{{ $field }}">
                @error('email')
                    <p class="{{ $error }}">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="{{ $submit }}">Simpan Profil</button>
            </div>
        </form>
    </section>

    <!-- 2. Sambungan akun Google -->
    <section class="{{ $card }}">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-start gap-3">
                <span class="w-9 h-9 rounded-full bg-sand-50 dark:bg-sand-925 border border-sand-200 dark:border-sand-850 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                </span>
                <div>
                    <h2 class="{{ $cardTitle }}">Akun Google</h2>
                    <p class="{{ $cardSub }}">
                        @if ($user->isGoogleBound())
                            Tersambung ke {{ $user->email ?? 'ID '.$user->google_id }}
                        @else
                            Masuk sekali klik tanpa mengetik kata sandi.
                        @endif
                    </p>
                </div>
            </div>
            @if ($user->isGoogleBound())
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-success-tint text-success-deep dark:bg-success-deep/40 dark:text-success-soft shrink-0">
                    <flux:icon name="check" class="w-3.5 h-3.5" /> Tersambung
                </span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-sand-100 text-sand-600 dark:bg-sand-900 dark:text-sand-400 shrink-0">
                    Belum tersambung
                </span>
            @endif
        </div>

        @if ($user->isGoogleBound())
            @if (! $hasPassword)
                <div class="flex gap-2.5 p-3.5 rounded-xl bg-warning-tint dark:bg-warning-deep/30 border border-warning/20 text-sm text-warning-deep dark:text-warning-soft">
                    <flux:icon name="exclamation-triangle" class="w-4 h-4 mt-0.5 shrink-0" />
                    <p>Anda belum punya kata sandi. Buat kata sandi di bawah sebelum melepas Google, agar tetap bisa masuk.</p>
                </div>
            @endif

            <form action="{{ route('account.google.unbind') }}" method="POST"
                  onsubmit="return confirm('Lepas sambungan akun Google ini?');">
                @csrf
                <button type="submit"
                    class="w-full sm:w-auto px-4 py-2.5 rounded-lg text-sm font-semibold text-danger dark:text-danger-soft border border-danger/30 hover:bg-danger-tint dark:hover:bg-danger-deep/30 transition">
                    Lepas sambungan Google
                </button>
            </form>
        @else
            <div class="flex gap-2.5 p-3.5 rounded-xl bg-danger-tint dark:bg-danger-deep/30 border border-danger/20 text-sm text-danger-deep dark:text-danger-soft">
                <flux:icon name="exclamation-triangle" class="w-4 h-4 mt-0.5 shrink-0" />
                <div>
                    <p class="font-semibold">Reset Kata Sandi Tidak Tersedia</p>
                    <p class="mt-0.5 leading-relaxed">Fitur lupa kata sandi hanya bisa dipakai akun yang tersambung ke Google. Sambungkan sekarang agar akun Anda bisa dipulihkan saat lupa sandi.</p>
                </div>
            </div>

            <a href="{{ route('auth.google.redirect') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-4 py-2.5 rounded-lg border border-sand-300 dark:border-sand-850 bg-white dark:bg-sand-950 hover:bg-sand-50 dark:hover:bg-sand-925 text-sm font-semibold text-sand-975 dark:text-sand-100 transition">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Sambungkan Akun Google</span>
            </a>
        @endif
    </section>

    <!-- 3. Kata sandi -->
    <section id="password-section" class="{{ $card }}">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="{{ $cardTitle }}">{{ $hasPassword ? 'Ubah kata sandi' : 'Buat kata sandi' }}</h2>
                <p class="{{ $cardSub }}">
                    @if ($hasPassword)
                        Gunakan minimal 6 karakter yang tidak dipakai di layanan lain.
                    @else
                        Akun ini dibuat lewat Google. Buat kata sandi agar bisa masuk dengan @<span>{{ $user->username }}</span>.
                    @endif
                </p>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold shrink-0 {{ $hasPassword ? 'bg-success-tint text-success-deep dark:bg-success-deep/40 dark:text-success-soft' : 'bg-warning-tint text-warning-deep dark:bg-warning-deep/40 dark:text-warning-soft' }}">
                {{ $hasPassword ? 'Aktif' : 'Belum ada' }}
            </span>
        </div>

        <form action="{{ route('account.updatePassword') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            @if ($hasPassword)
                <div class="sm:w-1/2 sm:pr-2">
                    <label for="current_password" class="{{ $label }}">Kata sandi saat ini</label>
                    <input type="password" id="current_password" name="current_password" required placeholder="••••••••" class="{{ $field }}">
                    @error('current_password')
                        <p class="{{ $error }}">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="new_password" class="{{ $label }}">Kata sandi baru</label>
                    <input type="password" id="new_password" name="password" required placeholder="Minimal 6 karakter" class="{{ $field }}">
                    @error('password')
                        <p class="{{ $error }}">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="new_password_confirmation" class="{{ $label }}">Ulangi kata sandi baru</label>
                    <input type="password" id="new_password_confirmation" name="password_confirmation" required placeholder="Ketik ulang" class="{{ $field }}">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="{{ $submit }}">{{ $hasPassword ? 'Perbarui Kata Sandi' : 'Simpan Kata Sandi' }}</button>
            </div>
        </form>
    </section>
</div>
@endsection
