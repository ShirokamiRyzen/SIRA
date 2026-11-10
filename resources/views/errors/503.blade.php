@extends('errors.layout')

@section('title', 'Layanan Sedang Dipelihara')
@section('code', '503')
@section('badge_class', 'bg-info-tint text-info-deep dark:bg-info-deep/30 dark:text-info-soft')
@section('dot_class', 'bg-info')

@section('message')
    {{ (isset($exception) && $exception->getMessage()) ? $exception->getMessage() : 'Sistem SIRA sedang menjalani pemeliharaan berkala untuk peningkatan performa dan keamanan. Kami akan segera kembali beroperasi secara normal.' }}
@endsection
