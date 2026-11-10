@extends('layouts.app')

@section('title', 'Lengkapi Pendaftaran Google — SIRA')

@section('content')
<div class="max-w-md mx-auto py-12 sm:py-20 px-4 relative">
    <!-- Subtle Ambient Warm Depth -->
    <div class="absolute -top-12 left-1/2 -translate-x-1/2 w-80 h-80 rounded-full bg-amber-500/[0.03] blur-[80px] pointer-events-none -z-10"></div>

    <div class="bg-white dark:bg-[#141414] border border-[#EAEAEA] dark:border-[#222222] rounded-[8px] p-8 sm:p-10 shadow-xs">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono tracking-wider uppercase bg-[#EDF3EC] text-[#346538] dark:bg-[#1C281E] dark:text-[#82C78A]">
                    Google OAuth
                </span>
                <span class="text-[10px] font-mono text-[#787774] dark:text-[#888888]">Langkah 2 dari 2</span>
            </div>

            <h1 class="font-sans text-2xl font-bold tracking-tight text-[#111111] dark:text-[#EDEDEC] leading-tight">
                Lengkapi Profil Akun Anda
            </h1>
            <p class="text-xs text-[#787774] dark:text-[#9B9B97] mt-1.5 leading-relaxed font-sans">
                Akun Google Anda berhasil diverifikasi. Masukkan username unik dan konfirmasi nama Anda untuk menyelesaikan registrasi.
            </p>
        </div>

        <!-- Google Profile Info Banner -->
        <div class="flex items-center space-x-3 p-3.5 mb-6 rounded-[6px] bg-[#F7F6F3] dark:bg-[#181818] border border-[#EAEAEA] dark:border-[#262626]">
            @if (!empty($googleData['avatar']))
                <img src="{{ $googleData['avatar'] }}" alt="Avatar Google" class="w-10 h-10 rounded-full object-cover border border-[#EAEAEA] dark:border-[#333333] shrink-0">
            @else
                <div class="w-10 h-10 rounded-full bg-[#EAEAEA] dark:bg-[#282828] text-[#111111] dark:text-[#EDEDEC] flex items-center justify-center text-sm font-bold font-mono shrink-0">
                    {{ strtoupper(substr($googleData['name'] ?? 'U', 0, 1)) }}
                </div>
            @endif
            <div class="min-w-0 flex-1">
                <div class="flex items-center space-x-1.5">
                    <span class="text-xs font-semibold text-[#111111] dark:text-[#EDEDEC] truncate">
                        {{ $googleData['name'] ?? 'Akun Google' }}
                    </span>
                    <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <p class="text-[11px] font-mono text-[#787774] dark:text-[#888888] truncate">
                    {{ $googleData['email'] ?? 'Email Terverifikasi' }}
                </p>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('auth.google.complete') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Nama Lengkap -->
            <div>
                <label for="name" class="block text-[11px] font-mono uppercase tracking-wider text-[#787774] dark:text-[#888888] mb-1.5">
                    Nama Lengkap <span class="text-[#9F2F2D]">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $googleData['name'] ?? '') }}" required autofocus
                    placeholder="Nama Lengkap Anda"
                    class="w-full px-3.5 py-2.5 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] placeholder-[#999999] focus:outline-none focus:border-[#111111] dark:focus:border-[#EDEDEC] transition duration-150">
                @error('name')
                    <p class="text-[11px] font-mono text-[#9F2F2D] mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Username -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="username" class="block text-[11px] font-mono uppercase tracking-wider text-[#787774] dark:text-[#888888]">
                        Username <span class="text-[#9F2F2D]">*</span>
                    </label>
                    <span class="text-[10px] font-mono text-[#999999]">3-30 karakter</span>
                </div>
                <input type="text" id="username" name="username" value="{{ old('username', $googleData['suggested_username'] ?? '') }}" required
                    placeholder="nama_pengguna_unik"
                    class="w-full px-3.5 py-2.5 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] placeholder-[#999999] focus:outline-none focus:border-[#111111] dark:focus:border-[#EDEDEC] transition duration-150">
                @error('username')
                    <p class="text-[11px] font-mono text-[#9F2F2D] mt-1.5">{{ $message }}</p>
                @enderror
                <p class="text-[10px] font-mono text-[#787774] dark:text-[#888888] mt-1">Digunakan untuk tag @mention pada komentar dan laporan.</p>
            </div>

            <!-- Password (Opsional) -->
            <div class="pt-2 border-t border-[#EAEAEA] dark:border-[#222222]">
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-[11px] font-mono uppercase tracking-wider text-[#787774] dark:text-[#888888]">
                        Kata Sandi <span class="text-[10px] lowercase text-[#999999] font-sans font-normal">(opsional)</span>
                    </label>
                    <span class="text-[10px] font-mono text-[#346538] dark:text-[#82C78A]">Dapat diatur nanti</span>
                </div>
                <input type="password" id="password" name="password"
                    placeholder="Kosongkan jika hanya ingin login via Google"
                    class="w-full px-3.5 py-2.5 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] placeholder-[#999999] focus:outline-none focus:border-[#111111] dark:focus:border-[#EDEDEC] transition duration-150">
                @error('password')
                    <p class="text-[11px] font-mono text-[#9F2F2D] mt-1.5">{{ $message }}</p>
                @enderror
                <p class="text-[10px] text-[#787774] dark:text-[#888888] mt-1 leading-normal">
                    Jika tidak diisi sekarang, Anda dapat membuat kata sandi sewaktu-waktu di <strong>Menu Akun</strong> agar bisa login dengan username.
                </p>
            </div>

            <!-- Konfirmasi Password -->
            <div>
                <label for="password_confirmation" class="block text-[11px] font-mono uppercase tracking-wider text-[#787774] dark:text-[#888888] mb-1.5">
                    Konfirmasi Kata Sandi <span class="text-[10px] lowercase text-[#999999] font-sans font-normal">(jika diisi)</span>
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    placeholder="Ulangi kata sandi di atas"
                    class="w-full px-3.5 py-2.5 rounded-[6px] border border-[#EAEAEA] dark:border-[#282828] bg-white dark:bg-[#181818] text-xs font-mono text-[#111111] dark:text-[#EDEDEC] placeholder-[#999999] focus:outline-none focus:border-[#111111] dark:focus:border-[#EDEDEC] transition duration-150">
            </div>

            <div class="pt-3">
                <button type="submit"
                    class="w-full py-2.5 px-4 bg-[#111111] hover:bg-[#2A2A2A] active:scale-[0.98] text-white dark:bg-[#EDEDEC] dark:text-[#111111] dark:hover:bg-[#FFFFFF] font-sans font-medium rounded-[6px] text-xs transition duration-150 flex items-center justify-center space-x-2">
                    <span>Selesaikan Pendaftaran &amp; Masuk</span>
                    <kbd class="border border-[#333333] dark:border-[#CCCCCC] rounded-[3px] bg-[#222222] dark:bg-[#E0E0E0] px-1 py-0.2 text-[9px] font-mono text-white dark:text-[#111111]">↵</kbd>
                </button>
            </div>
        </form>

        <div class="mt-6 pt-4 border-t border-[#EAEAEA] dark:border-[#222222] text-center text-xs text-[#787774] dark:text-[#888888]">
            <a href="{{ route('login') }}" class="hover:underline">Batal dan kembali ke halaman login</a>
        </div>
    </div>
</div>
@endsection

