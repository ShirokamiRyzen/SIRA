<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
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

    /**
     * Cari lokasi geografis berdasarkan kueri teks via Nominatim OpenStreetMap.
     */
    public function searchLocation(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        $cacheKey = 'geocode_search_'.md5(mb_strtolower($query));

        $results = Cache::remember($cacheKey, now()->addDay(), function () use ($query) {
            try {
                $response = Http::withUserAgent('SIRA-RuangAman/1.0 (https://sira.test)')
                    ->timeout(6)
                    ->get('https://nominatim.openstreetmap.org/search', [
                        'format' => 'json',
                        'q' => $query,
                        'limit' => 6,
                        'countrycodes' => 'id',
                        'addressdetails' => 1,
                    ]);

                if (! $response->successful()) {
                    return [];
                }

                $items = $response->json();

                if (! is_array($items)) {
                    return [];
                }

                return array_values(array_map(function (array $item): array {
                    return [
                        'name' => $item['name'] ?? $item['display_name'] ?? '',
                        'display_name' => $item['display_name'] ?? '',
                        'lat' => (float) ($item['lat'] ?? 0),
                        'lng' => (float) ($item['lon'] ?? 0),
                        'type' => $item['type'] ?? '',
                    ];
                }, $items));
            } catch (\Throwable) {
                return [];
            }
        });

        return response()->json($results);
    }
}
