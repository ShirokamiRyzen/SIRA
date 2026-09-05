@extends('layouts.app')

@section('title', 'SIRA — Sistem Informasi & Pelaporan Warga')

@section('content')
<div class="space-y-12">
    <!-- Component: Landing Showcase & Metrik -->
    <x-landing
        :total-reports="$totalReports"
        :critical-count="$criticalCount"
        :resolved-count="$resolvedCount"
    />

    <!-- Component: Dashboard Laporan, Filter, & Leaderboard -->
    <x-dashboard
        :reports="$reports"
        :available-cities="$availableCities"
        :available-districts="$availableDistricts"
        :critical-reports="$criticalReports"
        :sort="$sort"
    />
</div>
@endsection
