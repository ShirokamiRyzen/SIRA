@extends('layouts.app')

@section('title', 'Masuk — SIRA')

@section('content')
<div class="max-w-md mx-auto py-6 sm:py-16 relative">

    <div class="bg-white dark:bg-sand-975 border border-sand-200 dark:border-sand-900 rounded-2xl shadow-2xs p-6 sm:p-10">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-4">
                <img src="{{ asset('android-chrome-512x512.png') }}" alt="SIRA Logo" class="w-6 h-6 rounded-md object-contain shrink-0">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                    Autentikasi
                </span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-sand-975 dark:text-sand-100 leading-tight">
                Masuk ke akun Anda
            </h1>
            <p class="text-sm text-sand-600 dark:text-sand-400 mt-1.5 leading-relaxed">
                Gunakan akun Google atau kredensial username dan kata sandi Anda.
            </p>
        </div>

        <!-- Tombol Masuk dengan Google -->
        <div class="space-y-4">
            <a href="{{ route('auth.google.redirect') }}"
               class="w-full py-3 px-4 rounded-lg border border-sand-300 dark:border-sand-850 bg-white dark:bg-sand-950 hover:bg-sand-50 dark:hover:bg-sand-925 text-sm font-semibold text-sand-975 dark:text-sand-100 transition duration-150 flex items-center justify-center space-x-2.5 shadow-2xs active:scale-[0.99]">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Masuk dengan Google</span>
            </a>

            <div class="relative flex items-center justify-center my-4">
                <div class="absolute inset-x-0 top-1/2 border-t border-sand-200 dark:border-sand-900"></div>
                <span class="relative px-3 bg-white dark:bg-sand-975 text-xs text-sand-500 whitespace-nowrap">atau username</span>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="username" class="block text-sm font-semibold text-sand-800 dark:text-sand-200 mb-1.5">
                    Username
                </label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                    placeholder="nama_pengguna"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-sand-300 dark:border-sand-850 bg-white dark:bg-sand-950 text-sm text-sand-975 dark:text-sand-100 placeholder-sand-400 dark:placeholder-sand-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-150">
                @error('username')
                    <p class="text-sm text-danger dark:text-danger-soft mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                                        <label for="password" class="block text-sm font-semibold text-sand-800 dark:text-sand-200">
                                            Kata Sandi
                                        </label>
                                        <a href="{{ route('password.request') }}" class="text-xs text-sand-600 dark:text-sand-500 hover:text-sand-975 dark:hover:text-sand-100 hover:underline underline-offset-2">
                                            Lupa kata sandi?
                                        </a>
                </div>
                <input type="password" id="password" name="password" required
                    placeholder="••••••••"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-sand-300 dark:border-sand-850 bg-white dark:bg-sand-950 text-sm text-sand-975 dark:text-sand-100 placeholder-sand-400 dark:placeholder-sand-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-150">
                @error('password')
                    <p class="text-sm text-danger dark:text-danger-soft mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between py-1">
                <label class="flex items-center space-x-2 text-xs text-sand-600 dark:text-sand-500 cursor-pointer select-none">
                    <input type="checkbox" name="remember" class="rounded-md border-sand-100 dark:border-sand-850 text-sand-975 focus:ring-0">
                    <span class="text-xs">Tetap masuk di perangkat ini</span>
                </label>
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full py-3 px-4 btn-primary font-semibold rounded-lg text-sm transition duration-150 flex items-center justify-center space-x-2">
                    <span>Masuk</span>
                </button>
            </div>
        </form>

        <!-- Footer Card -->
        <div class="mt-8 pt-6 border-t border-sand-200 dark:border-sand-900 flex items-center justify-between gap-3 text-sm text-sand-600 dark:text-sand-500">
            <span>Belum memiliki akun?</span>
            <a href="{{ route('register') }}" class="font-semibold text-emerald-700 dark:text-emerald-400 hover:underline underline-offset-4">
                Daftar akun baru &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
