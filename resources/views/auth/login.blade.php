@extends('layouts.app')

@section('title', 'Masuk &mdash; SIRA')

@section('content')
<div class="max-w-md mx-auto py-16 sm:py-24 px-4 relative">
    <!-- Subtle Ambient Warm Depth -->
    <div class="absolute -top-12 left-1/2 -translate-x-1/2 w-80 h-80 rounded-full bg-amber-500/[0.03] blur-[80px] pointer-events-none -z-10"></div>

    <div class="bg-white dark:bg-[#141414] border border-[#EAEAEA] dark:border-[#222222] rounded-[8px] p-8 sm:p-10">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-6 h-6 rounded-[4px] bg-[#111111] text-white dark:bg-[#EDEDEC] dark:text-[#111111] flex items-center justify-center font-mono text-xs font-semibold">
                    S
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#EDF3EC] text-[#346538] dark:bg-[#1C281E] dark:text-[#82C78A]">
                    Autentikasi
                </span>
            </div>

            <h1 class="font-serif text-2xl sm:text-3xl font-normal tracking-tight text-[#111111] dark:text-[#EDEDEC] leading-tight">
                Masuk ke akun Anda
            </h1>
            <p class="text-xs text-[#787774] dark:text-[#9B9B97] mt-1.5 leading-relaxed font-sans">
                Masukkan kredensial username dan kata sandi Anda untuk mengakses platform pelaporan publik.
            </p>
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
