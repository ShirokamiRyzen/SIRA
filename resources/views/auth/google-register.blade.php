@extends('layouts.app')

@section('title', 'Lengkapi Pendaftaran Google — SIRA')

@section('content')
<div class="max-w-md mx-auto py-12 sm:py-20 px-4 relative">

    <div class="bg-white dark:bg-sand-975 border border-sand-200 dark:border-sand-900 rounded-2xl shadow-2xs p-6 sm:p-10 shadow-xs">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                    Google OAuth
                </span>
                <span class="text-xs text-sand-600 dark:text-sand-500">Langkah 2 dari 2</span>
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-sand-975 dark:text-sand-100 leading-tight">
                Lengkapi Profil Akun Anda
            </h1>
            <p class="text-sm text-sand-600 dark:text-sand-400 mt-1.5 leading-relaxed">
                Akun Google Anda berhasil diverifikasi. Masukkan username unik dan konfirmasi nama Anda untuk menyelesaikan registrasi.
            </p>
        </div>

        <!-- Google Profile Info Banner -->
        <div class="flex items-center space-x-3 p-3.5 mb-6 rounded-md bg-sand-50 dark:bg-sand-950 border border-sand-100 dark:border-sand-900">
            @if (!empty($googleData['avatar']))
                <img src="{{ $googleData['avatar'] }}" alt="Avatar Google" class="w-10 h-10 rounded-full object-cover border border-sand-100 dark:border-sand-800 shrink-0">
            @else
                <div class="w-10 h-10 rounded-full bg-sand-100 dark:bg-sand-850 text-sand-975 dark:text-sand-100 flex items-center justify-center text-sm font-bold shrink-0">
                    {{ strtoupper(substr($googleData['name'] ?? 'U', 0, 1)) }}
                </div>
            @endif
            <div class="min-w-0 flex-1">
                <div class="flex items-center space-x-1.5">
                    <span class="text-xs font-semibold text-sand-975 dark:text-sand-100 truncate">
                        {{ $googleData['name'] ?? 'Akun Google' }}
                    </span>
                    <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <p class="text-xs text-sand-600 dark:text-sand-500 truncate">
                    {{ $googleData['email'] ?? 'Email Terverifikasi' }}
                </p>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('auth.google.complete') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Nama Lengkap -->
            <div>
                <label for="name" class="block text-sm font-semibold text-sand-800 dark:text-sand-200 mb-1.5">
                    Nama Lengkap <span class="text-danger">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $googleData['name'] ?? '') }}" required autofocus
                    placeholder="Nama Lengkap Anda"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-sand-300 dark:border-sand-850 bg-white dark:bg-sand-950 text-sm text-sand-975 dark:text-sand-100 placeholder-sand-400 dark:placeholder-sand-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-150">
                @error('name')
                    <p class="text-sm text-danger dark:text-danger-soft mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Username -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="username" class="block text-sm font-semibold text-sand-800 dark:text-sand-200">
                        Username <span class="text-danger">*</span>
                    </label>
                    <span class="text-xs text-sand-500">3-30 karakter</span>
                </div>
                <input type="text" id="username" name="username" value="{{ old('username', $googleData['suggested_username'] ?? '') }}" required
                    placeholder="nama_pengguna_unik"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-sand-300 dark:border-sand-850 bg-white dark:bg-sand-950 text-sm text-sand-975 dark:text-sand-100 placeholder-sand-400 dark:placeholder-sand-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-150">
                @error('username')
                    <p class="text-sm text-danger dark:text-danger-soft mt-1.5">{{ $message }}</p>
                @enderror
                <p class="text-xs text-sand-600 dark:text-sand-500 mt-1">Digunakan untuk tag @mention pada komentar dan laporan.</p>
            </div>

            <!-- Password (Opsional) -->
            <div class="pt-2 border-t border-sand-100 dark:border-sand-900">
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm font-semibold text-sand-800 dark:text-sand-200">
                        Kata Sandi <span class="text-xs lowercase text-sand-500 font-normal">(opsional)</span>
                    </label>
                    <span class="text-xs text-success dark:text-success-soft">Dapat diatur nanti</span>
                </div>
                <input type="password" id="password" name="password"
                    placeholder="Kosongkan jika hanya ingin login via Google"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-sand-300 dark:border-sand-850 bg-white dark:bg-sand-950 text-sm text-sand-975 dark:text-sand-100 placeholder-sand-400 dark:placeholder-sand-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-150">
                @error('password')
                    <p class="text-sm text-danger dark:text-danger-soft mt-1.5">{{ $message }}</p>
                @enderror
                <p class="text-xs text-sand-600 dark:text-sand-500 mt-1 leading-normal">
                    Jika tidak diisi sekarang, Anda dapat membuat kata sandi sewaktu-waktu di <strong>Menu Akun</strong> agar bisa login dengan username.
                </p>
            </div>

            <!-- Konfirmasi Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-sand-800 dark:text-sand-200 mb-1.5">
                    Konfirmasi Kata Sandi <span class="text-xs lowercase text-sand-500 font-normal">(jika diisi)</span>
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    placeholder="Ulangi kata sandi di atas"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-sand-300 dark:border-sand-850 bg-white dark:bg-sand-950 text-sm text-sand-975 dark:text-sand-100 placeholder-sand-400 dark:placeholder-sand-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-150">
            </div>

            <div class="pt-3">
                <button type="submit"
                    class="w-full py-3 px-4 btn-primary font-semibold rounded-lg text-sm transition duration-150 flex items-center justify-center space-x-2">
                    <span>Selesaikan Pendaftaran &amp; Masuk</span>
                </button>
            </div>
        </form>

        <div class="mt-6 pt-4 border-t border-sand-100 dark:border-sand-900 text-center text-xs text-sand-600 dark:text-sand-500">
            <a href="{{ route('login') }}" class="hover:underline">Batal dan kembali ke halaman login</a>
        </div>
    </div>
</div>
@endsection

