@extends('errors.layout')

@section('title', 'Layanan Sedang Dipelihara')
@section('code', '503')
@section('badge_class', 'bg-sky-500/10 text-sky-700 dark:text-sky-400 border border-sky-500/20')
@section('dot_class', 'bg-sky-500')

@section('message')
    {{ (isset($exception) && $exception->getMessage()) ? $exception->getMessage() : 'Sistem SIRA sedang menjalani pemeliharaan berkala untuk peningkatan performa dan keamanan. Kami akan segera kembali beroperasi secara normal.' }}
@endsection
