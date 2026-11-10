@extends('errors.layout')

@section('title', 'Sesi Kedaluwarsa')
@section('code', '419')
@section('badge_class', 'bg-warning-tint text-warning-deep dark:bg-warning-deep/30 dark:text-warning-soft')
@section('dot_class', 'bg-warning')

@section('message')
    Sesi halaman Anda telah kedaluwarsa atau token keamanan form (CSRF) sudah tidak valid. Silakan muat ulang halaman dan ulangi pengiriman formulir Anda.
@endsection
