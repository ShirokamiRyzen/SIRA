@extends('layouts.app')

@section('title', 'SIRA — Sistem Informasi Ruang Aman')

@section('content')
<div class="space-y-12">
    <!-- Component: Landing Showcase & Metrik -->
    <x-landing
        :total-reports="$totalReports"
        :critical-count="$criticalCount"
        :urgent-count="$urgentCount"
        :resolved-count="$resolvedCount"
        :critical-reports="$criticalReports"
    />

    <!-- Component: Dashboard Laporan, Filter, & Leaderboard -->
    <x-dashboard
        :reports="$reports"
        :available-cities="$availableCities"
        :available-districts="$availableDistricts"
        :critical-reports="$criticalReports"
        :sort="$sort"
        :multi-issue-count="$multiIssueCount"
    />
</div>
@endsection
