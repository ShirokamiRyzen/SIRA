@extends('errors.layout')

@section('title', 'Terjadi Masalah pada Server')
@section('code', '500')
@section('badge_class', 'bg-danger-tint text-danger-deep dark:bg-danger-deep/30 dark:text-danger-soft')
@section('dot_class', 'bg-danger')

@section('message')
    Terjadi kendala teknis internal pada sistem kami. Tim pengembang telah mencatat insiden ini. Silakan coba beberapa saat lagi atau kembali ke beranda.
@endsection

@if (config('app.debug') && isset($exception) && !empty($exception->getMessage()))
@section('details')
    <div>
        <div class="font-bold text-rose-600 dark:text-rose-400 mb-1">Debug Info:</div>
        <div class="break-words">{{ $exception->getMessage() }}</div>
    </div>
@endsection
@endif
