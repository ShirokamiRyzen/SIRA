@extends('layouts.app')

@section('title', 'Masuk — SIRA')

@section('content')
<div class="max-w-md mx-auto py-16 sm:py-24 px-4 relative">
    <!-- Subtle Ambient Warm Depth -->
    <div class="absolute -top-12 left-1/2 -translate-x-1/2 w-80 h-80 rounded-full bg-amber-500/[0.03] blur-[80px] pointer-events-none -z-10"></div>

    <div class="bg-white dark:bg-[#141414] border border-[#EAEAEA] dark:border-[#222222] rounded-[8px] p-8 sm:p-10">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-4">
                <img src="{{ asset('android-chrome-512x512.png') }}" alt="SIRA Logo" class="w-6 h-6 rounded-[4px] object-contain shrink-0">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#EDF3EC] text-[#346538] dark:bg-[#1C281E] dark:text-[#82C78A]">
                    Autentikasi
                </span>
            </div>

            <h1 class="font-sans text-2xl sm:text-3xl font-bold tracking-tight text-[#111111] dark:text-[#EDEDEC] leading-tight">
                Masuk ke akun Anda
            </h1>
            <p class="text-xs text-[#787774] dark:text-[#9B9B97] mt-1.5 leading-relaxed font-sans">
                Gunakan akun Google atau kredensial username dan kata sandi Anda.
            </p>
        </div>

        <!-- Tombol Masuk dengan Google -->
        <div class="space-y-4">
            <a href="{{ route('auth.google.redirect') }}"
               class="w-full py-2.5 px-4 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] hover:bg-[#F7F6F3] dark:hover:bg-[#202020] text-xs font-medium text-[#111111] dark:text-[#EDEDEC] transition duration-150 flex items-center justify-center space-x-2.5 shadow-2xs active:scale-[0.99]">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Masuk dengan Google</span>
            </a>

            <div class="relative flex items-center justify-center my-4">
                <div class="w-full border-t border-[#EAEAEA] dark:border-[#242424]"></div>
                <span class="relative px-3 bg-white dark:bg-[#141414] text-[10px] font-mono uppercase tracking-widest text-[#999999]">atau username</span>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="username" class="block text-[11px] font-mono uppercase tracking-wider text-[#787774] dark:text-[#888888] mb-1.5">
                    Username
                </label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                    placeholder="nama_pengguna"
                    class="w-full px-3.5 py-2.5 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] placeholder-[#999999] focus:outline-none focus:border-[#111111] dark:focus:border-[#EDEDEC] transition duration-150">
                @error('username')
                    <p class="text-[11px] font-mono text-[#9F2F2D] mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-[11px] font-mono uppercase tracking-wider text-[#787774] dark:text-[#888888]">
                        Kata Sandi
                    </label>
                </div>
                <input type="password" id="password" name="password" required
                    placeholder="••••••••"
                    class="w-full px-3.5 py-2.5 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] placeholder-[#999999] focus:outline-none focus:border-[#111111] dark:focus:border-[#EDEDEC] transition duration-150">
                @error('password')
                    <p class="text-[11px] font-mono text-[#9F2F2D] mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between py-1">
                <label class="flex items-center space-x-2 text-xs text-[#787774] dark:text-[#9B9B97] cursor-pointer select-none">
                    <input type="checkbox" name="remember" class="rounded-[3px] border-[#EAEAEA] dark:border-[#282828] text-[#111111] focus:ring-0">
                    <span class="font-sans text-[11px]">Tetap masuk di perangkat ini</span>
                </label>
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full py-2.5 px-4 bg-[#111111] hover:bg-[#2A2A2A] active:scale-[0.98] text-white dark:bg-[#EDEDEC] dark:text-[#111111] dark:hover:bg-[#FFFFFF] font-sans font-medium rounded-[6px] text-xs transition duration-150 flex items-center justify-center space-x-2">
                    <span>Masuk</span>
                    <kbd class="border border-[#333333] dark:border-[#CCCCCC] rounded-[3px] bg-[#222222] dark:bg-[#E0E0E0] px-1 py-0.2 text-[9px] font-mono text-white dark:text-[#111111]">↵</kbd>
                </button>
            </div>
        </form>

        <!-- Footer Card -->
        <div class="mt-8 pt-6 border-t border-[#EAEAEA] dark:border-[#222222] flex items-center justify-between text-xs text-[#787774] dark:text-[#888888] font-sans">
            <span>Belum memiliki akun?</span>
            <a href="{{ route('register') }}" class="font-medium text-[#111111] dark:text-[#EDEDEC] hover:underline underline-offset-4">
                Daftar akun baru &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
