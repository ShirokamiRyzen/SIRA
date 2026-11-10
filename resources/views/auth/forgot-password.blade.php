@extends('layouts.app')

@section('title', 'Lupa Kata Sandi — SIRA')

@section('content')
<div class="max-w-md mx-auto py-6 sm:py-16 relative">

    <div class="bg-white dark:bg-sand-975 border border-sand-200 dark:border-sand-900 rounded-2xl shadow-2xs p-6 sm:p-10">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-4">
                <img src="{{ asset('android-chrome-512x512.png') }}" alt="SIRA Logo" class="w-6 h-6 rounded-md object-contain shrink-0">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                    Pemulihan
                </span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-sand-975 dark:text-sand-100 leading-tight">
                Lupa kata sandi?
            </h1>
            <p class="text-sm text-sand-600 dark:text-sand-400 mt-1.5 leading-relaxed">
                Masukkan email yang tertaut ke Google Account Anda. Kami akan mengirim tautan untuk membuat kata sandi baru.
            </p>
        </div>

        <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-sand-800 dark:text-sand-200 mb-1.5">
                    Alamat Email
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="contoh@domain.com"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-sand-300 dark:border-sand-850 bg-white dark:bg-sand-950 text-sm text-sand-975 dark:text-sand-100 placeholder-sand-400 dark:placeholder-sand-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-150">
                @error('email')
                    <p class="text-sm text-danger dark:text-danger-soft mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="p-3 rounded-md bg-sand-50 dark:bg-sand-950 border border-sand-100 dark:border-sand-900 text-xs text-sand-600 dark:text-sand-500 leading-relaxed">
                Hanya akun yang sudah menautkan Google Account yang dapat mereset kata sandi melalui email.
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full py-3 px-4 btn-primary font-semibold rounded-lg text-sm transition duration-150 flex items-center justify-center space-x-2">
                    <span>Kirim Tautan Reset</span>
                </button>
            </div>
        </form>

        <!-- Footer Card -->
        <div class="mt-8 pt-6 border-t border-sand-200 dark:border-sand-900 flex items-center justify-between gap-3 text-sm text-sand-600 dark:text-sand-500">
            <span>Ingat kata sandi Anda?</span>
            <a href="{{ route('login') }}" class="font-semibold text-emerald-700 dark:text-emerald-400 hover:underline underline-offset-4">
                Kembali masuk &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
