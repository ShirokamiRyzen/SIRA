<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HeatmapController extends Controller
{
    /**
     * Tampilkan halaman visualisasi Heatmap OpenFreeMap.
     */
    public function index(): View
    {
        $totalReports = Report::count();
        $tierCounts = [
            'critical' => Report::where('rank_tier', 'critical')->count(),
            'urgent' => Report::where('rank_tier', 'urgent')->count(),
            'trending' => Report::where('rank_tier', 'trending')->count(),
            'normal' => Report::where('rank_tier', 'normal')->count(),
        ];

        return view('reports.heatmap', compact('totalReports', 'tierCounts'));
    }

    /**
     * Endpoint API GeoJSON untuk dikonsumsi oleh layer Heatmap MapLibre GL JS.
     */
    public function geojson(Request $request): JsonResponse
    {
        $reports = Report::query()
            ->select([
                'id',
                'title',
                'latitude',
                'longitude',
                'vote_score',
                'upvotes_count',
                'downvotes_count',
                'rank_tier',
                'status',
                'city',
                'district',
                'formatted_address',
                'created_at',
            ])
            ->where('status', '!=', 'archived')
            ->get();

        $features = $reports->map(function (Report $report) {
            // Bobot intensitas panas (weight) minimal 1, ditambah vote_score
            // Critical tier mendapatkan pembobotan lebih tinggi agar menyala merah di heatmap
            $weightMultiplier = match ($report->rank_tier) {
                'critical' => 2.5,
                'urgent' => 1.8,
                'trending' => 1.2,
                default => 1.0,
            };

            $weight = max(1, ($report->vote_score + 1)) * $weightMultiplier;

            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$report->longitude, $report->latitude], // GeoJSON standard: [lng, lat]
                ],
                'properties' => [
                    'id' => $report->id,
                    'title' => $report->title,
                    'weight' => round($weight, 1),
                    'vote_score' => $report->vote_score,
                    'upvotes_count' => $report->upvotes_count,
                    'rank_tier' => $report->rank_tier,
                    'status' => $report->status,
                    'city' => $report->city,
                    'district' => $report->district,
                    'address' => $report->formatted_address,
                    'url' => route('reports.show', $report),
                    'date' => $report->created_at->diffForHumans(),
                ],
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}
