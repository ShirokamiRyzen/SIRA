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
     * Cari lokasi geografis cerdas multi-sumber (100% dinamis tanpa hardcode):
     * 1. Database Laporan SIRA lokal (laporan aktif masyarakat)
     * 2. ArcGIS World Geocoding Engine (POI, kampus, sekolah, instansi, fasilitas umum di Indonesia)
     * 3. Nominatim OpenStreetMap API (jalan, desa, kelurahan, kecamatan, kabupaten/kota resmi)
     * 4. Komoot Photon API (OSM Elasticsearch geocoder dengan pencarian fleksibel & typo)
     */
    public function searchLocation(Request $request): JsonResponse
    {
        $rawQuery = trim((string) $request->query('q', ''));

        if (mb_strlen($rawQuery) < 2) {
            return response()->json([]);
        }

        $cacheKey = 'geocode_smart_search_'.md5(mb_strtolower($rawQuery));

        $results = Cache::remember($cacheKey, now()->addHours(12), function () use ($rawQuery) {
            $collected = [];

            // 1. Database Laporan SIRA Lokal (jika ada laporan yang relevan)
            try {
                $localReports = Report::query()
                    ->where('status', '!=', 'archived')
                    ->where(function ($q) use ($rawQuery) {
                        $q->where('title', 'like', "%{$rawQuery}%")
                            ->orWhere('formatted_address', 'like', "%{$rawQuery}%")
                            ->orWhere('city', 'like', "%{$rawQuery}%")
                            ->orWhere('district', 'like', "%{$rawQuery}%");
                    })
                    ->take(3)
                    ->get();

                foreach ($localReports as $rep) {
                    $collected[] = [
                        'name' => '[Laporan] '.$rep->title,
                        'display_name' => $rep->formatted_address ?: ($rep->district ? $rep->district.', ' : '').($rep->city ?: 'Indonesia'),
                        'lat' => (float) $rep->latitude,
                        'lng' => (float) $rep->longitude,
                        'type' => 'report',
                    ];
                }
            } catch (\Throwable) {
                // Abaikan jika query database lokal gagal
            }

            // 2. ArcGIS World Geocoding Engine (Dukungan luas nama tempat, sekolah, kampus, instansi di Indonesia)
            try {
                $arcgisSuggestRes = Http::timeout(4)
                    ->get('https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/suggest', [
                        'text' => $rawQuery,
                        'f' => 'json',
                        'countryCode' => 'IDN',
                        'maxSuggestions' => 5,
                    ]);

                if ($arcgisSuggestRes->successful()) {
                    $suggestions = $arcgisSuggestRes->json('suggestions') ?? [];
                    $validSuggestions = array_filter($suggestions, fn ($s) => ! empty($s['magicKey']));

                    if (! empty($validSuggestions)) {
                        $poolResponses = Http::pool(function ($pool) use ($validSuggestions) {
                            foreach ($validSuggestions as $idx => $s) {
                                $pool->as("cand_{$idx}")
                                    ->timeout(4)
                                    ->get('https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/findAddressCandidates', [
                                        'magicKey' => $s['magicKey'],
                                        'f' => 'json',
                                        'maxLocations' => 1,
                                    ]);
                            }
                        });

                        foreach ($validSuggestions as $idx => $s) {
                            $key = "cand_{$idx}";
                            if (isset($poolResponses[$key]) && $poolResponses[$key]->successful()) {
                                $cand = $poolResponses[$key]->json('candidates.0');
                                if ($cand && isset($cand['location']['x'], $cand['location']['y'])) {
                                    $collected[] = [
                                        'name' => $cand['address'] ?? explode(',', $s['text'])[0] ?? $s['text'],
                                        'display_name' => $s['text'] ?? $cand['address'],
                                        'lat' => (float) $cand['location']['y'],
                                        'lng' => (float) $cand['location']['x'],
                                        'type' => 'poi',
                                    ];
                                }
                            }
                        }
                    }
                }
            } catch (\Throwable) {
                // Lanjut ke provider berikutnya jika terjadi kendala jaringan
            }

            // Normalisasi variasi kata kunci untuk OpenStreetMap
            $cleaned = trim(preg_replace(
                '/\b(stt|sma|sman|smk|smkn|smp|smpn|sd|sdn|rs|rsud|rsia|rumah sakit|universitas|univ|institut|politeknik|kampus|kecamatan|kec|kelurahan|kel|desa|ds|jalan|jl|gang|gg)\b/i',
                '',
                $rawQuery
            ));
            $cleaned = preg_replace('/\s+/', ' ', $cleaned);

            $searchQueries = array_unique(array_filter([$rawQuery, $cleaned]));

            // 3. Nominatim OpenStreetMap API (Wilayah administratif, kota, kecamatan, dan jalan)
            foreach ($searchQueries as $sq) {
                if (mb_strlen($sq) < 2) {
                    continue;
                }

                try {
                    $nomRes = Http::withUserAgent('SIRA-RuangAman/1.0 (https://sira.test)')
                        ->timeout(4)
                        ->get('https://nominatim.openstreetmap.org/search', [
                            'format' => 'json',
                            'q' => $sq,
                            'limit' => 5,
                            'countrycodes' => 'id',
                            'addressdetails' => 1,
                        ]);

                    if ($nomRes->successful()) {
                        $items = $nomRes->json();
                        if (is_array($items)) {
                            foreach ($items as $item) {
                                $collected[] = [
                                    'name' => $item['name'] ?? $item['display_name'] ?? '',
                                    'display_name' => $item['display_name'] ?? '',
                                    'lat' => (float) ($item['lat'] ?? 0),
                                    'lng' => (float) ($item['lon'] ?? 0),
                                    'type' => $item['type'] ?? '',
                                ];
                            }
                        }
                    }
                } catch (\Throwable) {
                    // Abaikan jika timeout
                }
            }

            // 4. Komoot Photon API (OSM Elasticsearch geocoder dengan pencarian fuzzy)
            foreach ($searchQueries as $sq) {
                if (mb_strlen($sq) < 2) {
                    continue;
                }

                try {
                    $photonRes = Http::timeout(4)
                        ->get('https://photon.komoot.io/api/', [
                            'q' => $sq,
                            'limit' => 5,
                        ]);

                    if ($photonRes->successful()) {
                        $features = $photonRes->json('features') ?? [];
                        foreach ($features as $feat) {
                            $props = $feat['properties'] ?? [];
                            $coords = $feat['geometry']['coordinates'] ?? [];
                            if (count($coords) >= 2) {
                                $parts = array_filter([
                                    $props['name'] ?? null,
                                    $props['street'] ?? null,
                                    $props['district'] ?? null,
                                    $props['city'] ?? $props['county'] ?? null,
                                    $props['state'] ?? null,
                                    $props['country'] ?? 'Indonesia',
                                ]);

                                $collected[] = [
                                    'name' => $props['name'] ?? reset($parts) ?: $sq,
                                    'display_name' => implode(', ', array_unique($parts)),
                                    'lat' => (float) $coords[1],
                                    'lng' => (float) $coords[0],
                                    'type' => $props['osm_value'] ?? $props['type'] ?? 'place',
                                ];
                            }
                        }
                    }
                } catch (\Throwable) {
                    // Lanjut jika timeout
                }
            }

            // 5. Deduplikasi Cerdas (Berdasarkan kemiripan koordinat < 80 meter)
            $unique = [];
            foreach ($collected as $cand) {
                if ($cand['lat'] == 0 && $cand['lng'] == 0) {
                    continue;
                }

                $isDuplicate = false;
                foreach ($unique as $existing) {
                    $dLat = abs($existing['lat'] - $cand['lat']);
                    $dLng = abs($existing['lng'] - $cand['lng']);

                    if ($dLat < 0.0008 && $dLng < 0.0008) {
                        $isDuplicate = true;
                        break;
                    }
                }

                if (! $isDuplicate) {
                    $unique[] = $cand;
                }
            }

            return array_slice($unique, 0, 8);
        });

        return response()->json($results);
    }
}
