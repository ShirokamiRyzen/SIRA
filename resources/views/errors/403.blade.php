@extends('errors.layout')

@section('title', 'Akses Ditolak')
@section('code', '403')
@section('badge_class', 'bg-danger-tint text-danger-deep dark:bg-danger-deep/30 dark:text-danger-soft')
@section('dot_class', 'bg-danger')

@section('message')
    {{ (isset($exception) && $exception->getMessage()) ? $exception->getMessage() : 'Anda tidak memiliki hak akses yang memadai untuk membuka halaman atau melakukan tindakan ini.' }}
@endsection
