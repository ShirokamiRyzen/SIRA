@extends('errors.layout')

@section('title', 'Akses Ditolak')
@section('code', '403')
@section('badge_class', 'bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-500/20')
@section('dot_class', 'bg-rose-500')

@section('message')
    {{ (isset($exception) && $exception->getMessage()) ? $exception->getMessage() : 'Anda tidak memiliki hak akses yang memadai untuk membuka halaman atau melakukan tindakan ini.' }}
@endsection
