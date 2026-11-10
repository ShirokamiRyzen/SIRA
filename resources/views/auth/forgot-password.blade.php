@extends('layouts.app')

@section('title', 'Lupa Kata Sandi — SIRA')

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
                    Pemulihan
                </span>
            </div>

            <h1 class="font-sans text-2xl sm:text-3xl font-bold tracking-tight text-[#111111] dark:text-[#EDEDEC] leading-tight">
                Lupa kata sandi?
            </h1>
            <p class="text-xs text-[#787774] dark:text-[#9B9B97] mt-1.5 leading-relaxed font-sans">
                Masukkan email yang tertaut ke Google Account Anda. Kami akan mengirim tautan untuk membuat kata sandi baru.
            </p>
        </div>

        <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-[11px] font-mono uppercase tracking-wider text-[#787774] dark:text-[#888888] mb-1.5">
                    Alamat Email
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="contoh@domain.com"
                    class="w-full px-3.5 py-2.5 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] placeholder-[#999999] focus:outline-none focus:border-[#111111] dark:focus:border-[#EDEDEC] transition duration-150">
                @error('email')
                    <p class="text-[11px] font-mono text-[#9F2F2D] mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="p-3 rounded-[6px] bg-[#F7F6F3] dark:bg-[#1A1A1A] border border-[#EAEAEA] dark:border-[#262626] text-[11px] text-[#787774] dark:text-[#9B9B97] leading-relaxed font-sans">
                Hanya akun yang sudah menautkan Google Account yang dapat mereset kata sandi melalui email.
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full py-2.5 px-4 bg-[#111111] hover:bg-[#2A2A2A] active:scale-[0.98] text-white dark:bg-[#EDEDEC] dark:text-[#111111] dark:hover:bg-[#FFFFFF] font-sans font-medium rounded-[6px] text-xs transition duration-150 flex items-center justify-center space-x-2">
                    <span>Kirim Tautan Reset</span>
                </button>
            </div>
        </form>

        <!-- Footer Card -->
        <div class="mt-8 pt-6 border-t border-[#EAEAEA] dark:border-[#222222] flex items-center justify-between text-xs text-[#787774] dark:text-[#888888] font-sans">
            <span>Ingat kata sandi Anda?</span>
            <a href="{{ route('login') }}" class="font-medium text-[#111111] dark:text-[#EDEDEC] hover:underline underline-offset-4">
                Kembali masuk &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
