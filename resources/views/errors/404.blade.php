@extends('errors.layout')

@section('title', 'Halaman Tidak Ditemukan')
@section('code', '404')
@section('badge_class', 'bg-warning-tint text-warning-deep dark:bg-warning-deep/30 dark:text-warning-soft')
@section('dot_class', 'bg-warning')

@section('message')
    Halaman atau pengaduan yang Anda cari tidak ditemukan. Halaman mungkin telah dipindahkan, dihapus, atau tautan yang Anda tuju salah ketik.
@endsection
