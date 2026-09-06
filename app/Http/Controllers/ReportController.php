<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportVote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Daftar 30 kecamatan resmi di Kota Bandung.
     *
     * @var array<int, string>
     */
    public static array $officialBandungDistricts = [
        'Andir',
        'Antapani',
        'Arcamanik',
        'Astanaanyar',
        'Babakan Ciparay',
        'Bandung Kidul',
        'Bandung Kulon',
        'Bandung Wetan',
        'Batununggal',
        'Bojongloa Kaler',
        'Bojongloa Kidul',
        'Buahbatu',
        'Cibeunying Kaler',
        'Cibeunying Kidul',
        'Cibiru',
        'Cicendo',
        'Cidadap',
        'Cinambo',
        'Coblong',
        'Gedebage',
        'Kiaracondong',
        'Lengkong',
        'Mandalajati',
        'Panyileukan',
        'Rancasari',
        'Regol',
        'Sukajadi',
        'Sukasari',
        'Sumur Bandung',
        'Ujungberung',
    ];

    /**
     * Tampilkan daftar feed laporan & leaderboard dengan filter daerah.
     */
    public function index(Request $request): View
    {
        $query = Report::query()->with(['user'])->withCount('comments');

        // Pastikan laporan lama yang memiliki subdistrict/city tetapi district masih null diperbarui secara otomatis
        Report::where(function ($q) {
            $q->whereNull('district')->orWhere('district', '');
        })->whereNotNull('subdistrict')->where('subdistrict', '!=', '')->update([
            'district' => DB::raw('subdistrict'),
        ]);

        Report::where(function ($q) {
            $q->whereNull('district')->orWhere('district', '');
        })->whereNotNull('city')->where('city', '!=', '')->update([
            'district' => DB::raw('city'),
        ]);

        // Khusus laporan Purwakarta, sinkronkan district agar 'Purwakarta' muncul sebagai wilayah/kecamatan
        Report::where(function ($q) {
            $q->where('city', 'like', '%Purwakarta%')
                ->orWhere('formatted_address', 'like', '%Purwakarta%');
        })->where(function ($q) {
            $q->where('district', 'Cikopak')
                ->orWhereNull('district')
                ->orWhere('district', '');
        })->update([
            'district' => 'Purwakarta',
            'city' => 'Purwakarta',
        ]);

        // Filter pencarian teks
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('formatted_address', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('district', 'like', "%{$search}%")
                    ->orWhere('subdistrict', 'like', "%{$search}%");
            });
        }

        // Filter kota/kabupaten
        if ($city = $request->input('city')) {
            $query->where(function ($q) use ($city) {
                $q->where('city', $city)
                    ->orWhere('district', $city)
                    ->orWhere('formatted_address', 'like', "%{$city}%");
            });
        }

        // Filter kecamatan / wilayah
        if ($district = $request->input('district')) {
            $query->where(function ($q) use ($district) {
                $q->where('district', $district)
                    ->orWhere('subdistrict', $district)
                    ->orWhere('city', $district)
                    ->orWhere('formatted_address', 'like', "%{$district}%");
            });
        }

        // Filter rank tier
        if ($tier = $request->input('rank_tier')) {
            $query->where('rank_tier', $tier);
        }

        // Filter status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Pengurutan / Ranking
        $sort = $request->input('sort', 'trending');
        match ($sort) {
            'latest' => $query->latest('created_at'),
            'top_score' => $query->orderByDesc('vote_score')->latest('created_at'),
            'most_upvoted' => $query->orderByDesc('upvotes_count')->latest('created_at'),
            default => $query->orderByDesc('vote_score')->orderByDesc('created_at'), // Trending
        };

        $reports = $query->paginate(9)->withQueryString()->fragment('dashboard');

        // Ambil daftar unik kota & kecamatan untuk dropdown filter (disortir alfabetis A-Z)
        $availableCities = Report::whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->pluck('city')
            ->filter()
            ->unique()
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $selectedCity = $request->input('city');
        $dbDistrictsQuery = Report::whereNotNull('district')->where('district', '!=', '');
        if ($selectedCity) {
            $dbDistrictsQuery->where(function ($q) use ($selectedCity) {
                $q->where('city', $selectedCity)
                    ->orWhere('district', $selectedCity)
                    ->orWhere('formatted_address', 'like', "%{$selectedCity}%");
            });
        }
        $dbDistricts = $dbDistrictsQuery->distinct()->pluck('district');

        // Ambil juga daftar wilayah/kota non-Bandung (misalnya Purwakarta) agar user dapat langsung memilihnya dari dropdown kecamatan
        $otherLocations = Report::whereNotNull('city')
            ->where('city', '!=', '')
            ->where('city', 'not like', '%Bandung%')
            ->distinct()
            ->pluck('city');

        if (empty($selectedCity) || str_contains(strtolower($selectedCity), 'bandung')) {
            $availableDistricts = collect(self::$officialBandungDistricts)
                ->merge($dbDistricts)
                ->merge($otherLocations)
                ->filter()
                ->unique()
                ->sort(SORT_NATURAL | SORT_FLAG_CASE)
                ->values();
        } else {
            $availableDistricts = $dbDistricts
                ->push($selectedCity)
                ->filter()
                ->unique()
                ->sort(SORT_NATURAL | SORT_FLAG_CASE)
                ->values();
        }

        // Top 5 Laporan Terkritis untuk leaderboard sidebar / widget
        $criticalReports = Report::where('rank_tier', '!=', 'normal')
            ->orderByDesc('vote_score')
            ->take(5)
            ->get();

        // Statistik Agregat untuk Komponen Landing
        $totalReports = Report::count();
        $criticalCount = Report::where('rank_tier', 'critical')->count();
        $resolvedCount = Report::where('status', 'resolved')->count();

        return view('reports.index', compact(
            'reports',
            'availableCities',
            'availableDistricts',
            'criticalReports',
            'sort',
            'totalReports',
            'criticalCount',
            'resolvedCount'
        ));
    }

    /**
     * Tampilkan form pembuatan laporan baru.
     */
    public function create(): View
    {
        return view('reports.create');
    }

    /**
     * Simpan laporan baru dengan foto Base64 dan koordinat geolokasi.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:3000'],
            'image_base64' => ['required', 'string'], // Hasil kompresi 80% dari canvas
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'province' => ['nullable', 'string', 'max:150'],
            'city' => ['nullable', 'string', 'max:150'],
            'district' => ['nullable', 'string', 'max:150'],
            'subdistrict' => ['nullable', 'string', 'max:150'],
            'formatted_address' => ['nullable', 'string', 'max:500'],
            'osm_place_id' => ['nullable', 'string', 'max:100'],
        ]);

        $geohash = $this->encodeGeohash((float) $validated['latitude'], (float) $validated['longitude'], 8);

        $district = $validated['district'] ?? null;
        if (empty($district)) {
            $district = $validated['subdistrict'] ?? $validated['city'] ?? null;
        }

        $report = Report::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image_base64' => $validated['image_base64'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'geohash' => $geohash,
            'province' => $validated['province'] ?? null,
            'city' => $validated['city'] ?? null,
            'district' => $district,
            'subdistrict' => $validated['subdistrict'] ?? null,
            'formatted_address' => $validated['formatted_address'] ?? null,
            'osm_place_id' => $validated['osm_place_id'] ?? null,
            'rank_tier' => 'normal',
            'status' => 'active',
        ]);

        return redirect()->route('reports.show', $report)
            ->with('success', 'Laporan berhasil dipublikasikan! Komunitas dapat segera memberikan vote.');
    }

    /**
     * Tampilkan detail laporan beserta peta lokasi dan thread komentar bertingkat.
     */
    public function show(Report $report): View
    {
        $report->load([
            'user',
            'rootComments' => function ($query) {
                $query->with(['user', 'replies.user'])->latest();
            },
        ]);

        $userVote = Auth::check() ? $report->userVote(Auth::user()) : null;

        return view('reports.show', compact('report', 'userVote'));
    }

    /**
     * Berikan Like (+1) atau Dislike (-1) pada laporan, dengan fitur toggle/unvote.
     */
    public function vote(Request $request, Report $report): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'value' => ['required', 'in:1,-1'],
        ]);

        $value = (int) $validated['value'];
        $userId = Auth::id();

        DB::transaction(function () use ($report, $userId, $value) {
            $existingVote = ReportVote::where('report_id', $report->id)
                ->where('user_id', $userId)
                ->first();

            if ($existingVote) {
                if ($existingVote->value === $value) {
                    // Klik tombol yang sama -> batalkan vote (unvote)
                    $existingVote->delete();
                } else {
                    // Ganti vote (misal dari dislike menjadi like)
                    $existingVote->update(['value' => $value]);
                }
            } else {
                ReportVote::create([
                    'report_id' => $report->id,
                    'user_id' => $userId,
                    'value' => $value,
                ]);
            }

            $report->recalculateVoteStatsAndTier();
        });

        $report->refresh();
        $currentUserVote = $report->userVote(Auth::user());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'user_vote' => $currentUserVote ? $currentUserVote->value : 0,
                'upvotes_count' => $report->upvotes_count,
                'downvotes_count' => $report->downvotes_count,
                'vote_score' => $report->vote_score,
                'rank_tier' => $report->rank_tier,
            ]);
        }

        return back()->with('success', 'Vote berhasil diperbarui!');
    }

    /**
     * Perbarui status laporan (khusus hanya oleh pembuat laporan).
     */
    public function updateStatus(Request $request, Report $report): JsonResponse|RedirectResponse
    {
        if (Auth::id() !== $report->user_id) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pembuat laporan yang dapat mengubah status laporan ini.',
                ], 403);
            }

            abort(403, 'Hanya pembuat laporan yang dapat mengubah status laporan ini.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:active,in_progress,resolved'],
        ]);

        $report->update(['status' => $validated['status']]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $report->status,
                'status_label' => str_replace('_', ' ', $report->status),
                'message' => 'Status laporan berhasil diperbarui menjadi '.str_replace('_', ' ', $report->status).'.',
            ]);
        }

        return back()->with('success', 'Status laporan berhasil diperbarui!');
    }

    /**
     * Konversi koordinat lat/lon menjadi Geohash string untuk pengelompokan area.
     */
    private function encodeGeohash(float $lat, float $lon, int $precision = 8): string
    {
        $base32 = '0123456789bcdefghjkmnpqrstuvwxyz';
        $minLat = -90.0;
        $maxLat = 90.0;
        $minLon = -180.0;
        $maxLon = 180.0;

        $geohash = '';
        $isEven = true;
        $bit = 0;
        $ch = 0;

        while (strlen($geohash) < $precision) {
            if ($isEven) {
                $mid = ($minLon + $maxLon) / 2;
                if ($lon >= $mid) {
                    $ch |= (1 << (4 - $bit));
                    $minLon = $mid;
                } else {
                    $maxLon = $mid;
                }
            } else {
                $mid = ($minLat + $maxLat) / 2;
                if ($lat >= $mid) {
                    $ch |= (1 << (4 - $bit));
                    $minLat = $mid;
                } else {
                    $maxLat = $mid;
                }
            }

            $isEven = ! $isEven;
            if ($bit < 4) {
                $bit++;
            } else {
                $geohash .= $base32[$ch];
                $bit = 0;
                $ch = 0;
            }
        }

        return $geohash;
    }
}
