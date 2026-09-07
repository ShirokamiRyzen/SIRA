@extends('errors.layout')

@section('title', 'Sesi Kedaluwarsa')
@section('code', '419')
@section('badge_class', 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20')
@section('dot_class', 'bg-amber-500')

@section('message')
    Sesi halaman Anda telah kedaluwarsa atau token keamanan form (CSRF) sudah tidak valid. Silakan muat ulang halaman dan ulangi pengiriman formulir Anda.
@endsection
