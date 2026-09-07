@extends('errors.layout')

@section('title', 'Terlalu Banyak Permintaan')
@section('code', '429')
@section('badge_class', 'bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-500/20')
@section('dot_class', 'bg-blue-500')

@section('message')
    Sistem mendeteksi terlalu banyak permintaan dalam waktu singkat (rate limit). Mohon tunggu beberapa saat sebelum mencoba kembali untuk menjaga kestabilan layanan.
@endsection
