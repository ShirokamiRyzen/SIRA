@extends('errors.layout')

@section('title', 'Terlalu Banyak Permintaan')
@section('code', '429')
@section('badge_class', 'bg-info-tint text-info-deep dark:bg-info-deep/30 dark:text-info-soft')
@section('dot_class', 'bg-info')

@section('message')
    Sistem mendeteksi terlalu banyak permintaan dalam waktu singkat (rate limit). Mohon tunggu beberapa saat sebelum mencoba kembali untuk menjaga kestabilan layanan.
@endsection
