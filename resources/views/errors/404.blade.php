@extends('errors.layout')

@section('title', 'Halaman Tidak Ditemukan')
@section('code', '404')
@section('badge_class', 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20')
@section('dot_class', 'bg-amber-500')

@section('message')
    Halaman atau pengaduan yang Anda cari tidak ditemukan. Halaman mungkin telah dipindahkan, dihapus, atau tautan yang Anda tuju salah ketik.
@endsection
