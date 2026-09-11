<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportVote;
use App\Models\User;
use App\Notifications\ReportMentionNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Tampilkan daftar feed laporan & leaderboard dengan filter daerah.
     */
    public function index(Request $request): View
    {
        $query = Report::query()
            ->withMultiIssueStatus()
            ->with(['user:id,name,username,is_admin,is_verified'])
            ->withCount('comments');

        $this->applyFilters($query, $request);

        $sort = $request->input('sort', 'trending');
        match ($sort) {
            'latest' => $query->latest('created_at'),
            'top_score' => $query->orderByDesc('vote_score')->latest('created_at'),
            'most_upvoted' => $query->orderByDesc('upvotes_count')->latest('created_at'),
            default => $query->orderByDesc('vote_score')->orderByDesc('created_at'),
        };

        $reports = $query->paginate(9)->withQueryString()->fragment('dashboard');

        Cache::forget('reports_filter_available_cities');

        $cachedCities = Cache::remember('sira_reports_available_cities', 300, function () {
            return Report::whereNotNull('city')
                ->where('city', '!=', '')
                ->distinct()
                ->pluck('city')
                ->filter()
                ->unique()
                ->sort(SORT_NATURAL | SORT_FLAG_CASE)
                ->values()
                ->all();
        });

        $availableCities = collect(is_iterable($cachedCities) ? $cachedCities : []);

        $availableDistricts = $this->getAvailableDistricts($request->input('city'));

        $criticalReports = Report::withMultiIssueStatus()
            ->with(['user:id,name,username,is_admin,is_verified'])
            ->where('rank_tier', '!=', 'normal')
            ->orderByDesc('vote_score')
            ->take(5)
            ->get();

        $stats = $this->getAggregateStats();

        return view('reports.index', array_merge([
            'reports' => $reports,
            'availableCities' => $availableCities,
            'availableDistricts' => $availableDistricts,
            'criticalReports' => $criticalReports,
            'sort' => $sort,
            'myReportsCount' => Auth::check() ? Auth::user()->reports()->count() : 0,
        ], $stats));
    }

    /**
     * Terapkan parameter filter pencarian dan taksonomi laporan pada query builder.
     */
    protected function applyFilters(Builder $query, Request $request): void
    {
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

        if ($city = $request->input('city')) {
            $query->where(function ($q) use ($city) {
                $q->where('city', $city)
                    ->orWhere('district', $city)
                    ->orWhere('formatted_address', 'like', "%{$city}%");
            });
        }

        if ($district = $request->input('district')) {
            $query->where(function ($q) use ($district) {
                $q->where('district', $district)
                    ->orWhere('subdistrict', $district)
                    ->orWhere('city', $district)
                    ->orWhere('formatted_address', 'like', "%{$district}%");
            });
        }

        if ($tier = $request->input('rank_tier')) {
            $query->where('rank_tier', $tier);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $issueType = $request->input('issue_type');
        if ($issueType === 'multi' || $request->boolean('multi_issue')) {
            $query->onlyMultiIssue();
        } elseif ($issueType === 'single') {
            $query->onlySingleIssue();
        }

        if ($request->boolean('my_reports') && Auth::check()) {
            $query->where('user_id', Auth::id());
        }
    }

    /**
     * Dapatkan daftar wilayah/kecamatan yang benar-benar ada datanya di basis data.
     *
     * @return Collection<int, string>
     */
    public static function getAvailableDistricts(?string $selectedCity): Collection
    {
        $query = Report::whereNotNull('district')
            ->where('district', '!=', '');

        if ($selectedCity) {
            $query->where(function ($q) use ($selectedCity) {
                $q->where('city', $selectedCity)
                    ->orWhere('formatted_address', 'like', "%{$selectedCity}%");
            });
        }

        return $query->distinct()
            ->pluck('district')
            ->filter()
            ->unique()
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    /**
     * Dapatkan ringkasan statistik agregat laporan untuk dasbor.
     *
     * @return array<string, int>
     */
    protected function getAggregateStats(): array
    {
        $stats = Report::query()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN rank_tier = "critical" THEN 1 ELSE 0 END) as critical,
                SUM(CASE WHEN rank_tier = "urgent" THEN 1 ELSE 0 END) as urgent,
                SUM(CASE WHEN status = "resolved" THEN 1 ELSE 0 END) as resolved
            ')
            ->first();

        return [
            'totalReports' => (int) ($stats->total ?? 0),
            'criticalCount' => (int) ($stats->critical ?? 0),
            'urgentCount' => (int) ($stats->urgent ?? 0),
            'resolvedCount' => (int) ($stats->resolved ?? 0),
            'multiIssueCount' => Report::onlyMultiIssue()->count(),
        ];
    }

    /**
     * Tampilkan form pembuatan laporan baru.
     */
    public function create(): View
    {
        $categories = Report::CATEGORIES;

        return view('reports.create', compact('categories'));
    }

    /**
     * Simpan laporan baru dengan foto Base64 dan koordinat geolokasi.
     */
    public function store(Request $request): RedirectResponse
    {
        $legacyMap = [
            'infrastruktur' => 'jalan_jembatan',
            'kelistrikan' => 'lampu_pju',
            'lingkungan' => 'sampah_kebersihan',
            'fasilitas_umum' => 'taman_fasum',
            'bencana_alam' => 'drainase_saluran',
            'kebakaran' => 'sampah_kebersihan',
        ];
        $allowedCategories = array_unique(array_merge(array_keys(Report::CATEGORIES), array_keys($legacyMap)));

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'category' => ['nullable', 'string', 'in:'.implode(',', $allowedCategories)],
            'description' => [
                'required',
                'string',
                'max:3000',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $wordCount = count(preg_split('/\s+/u', trim((string) $value), -1, PREG_SPLIT_NO_EMPTY));
                    if ($wordCount < 5) {
                        $fail('Deskripsi laporan minimal harus terdiri dari 5 kata agar informasi masalah lengkap dan jelas.');
                    }
                },
            ],
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

        $categoryKey = $validated['category'] ?? 'jalan_jembatan';
        if (isset($legacyMap[$categoryKey])) {
            $categoryKey = $legacyMap[$categoryKey];
        }

        $report = Report::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'category' => $categoryKey,
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

        // Kirim notifikasi mention (@) jika ada akun pengguna/lembaga yang ditandai dalam postingan
        $this->dispatchReportMentionNotifications($report);

        Cache::forget('sira_reports_available_cities');
        Cache::forget('reports_filter_available_cities');
        Cache::forget('top_5_reporters_modal');

        return redirect()->route('reports.show', $report)
            ->with('success', 'Laporan berhasil dipublikasikan! Komunitas dapat segera memberikan vote.');
    }

    /**
     * Tampilkan detail laporan, komentar bertingkat, dan laporan multi-masalah di lokasi yang sama.
     */
    public function show(Report $report, Request $request): View
    {
        $report->load([
            'user:id,name,username,is_admin,is_verified',
            'rootComments' => function ($query) {
                $query->with([
                    'user:id,name,username,is_admin,is_verified',
                    'replies.user:id,name,username,is_admin,is_verified',
                ])->latest();
            },
        ]);

        $userVote = Auth::check() ? $report->userVote(Auth::user()) : null;

        // Ambil laporan-laporan lain di titik lokasi/koordinat yang sama persis (Co-located Reports)
        $totalCoLocatedCount = Report::where('latitude', $report->latitude)
            ->where('longitude', $report->longitude)
            ->where('id', '!=', $report->id)
            ->count();

        $coLocatedQuery = Report::where('latitude', $report->latitude)
            ->where('longitude', $report->longitude)
            ->where('id', '!=', $report->id)
            ->with(['user:id,name,username,is_admin,is_verified'])
            ->withCount('comments');

        // Filter scoped khusus lokasi ini (urgent, active, resolved)
        $coFilter = $request->input('co_filter');
        if ($coFilter) {
            match ($coFilter) {
                'urgent' => $coLocatedQuery->whereIn('rank_tier', ['urgent', 'critical']),
                'active' => $coLocatedQuery->where('status', 'active'),
                'resolved' => $coLocatedQuery->where('status', 'resolved'),
                default => null,
            };
        }

        // Paginasi khusus untuk daftar masalah di lokasi ini (4 per halaman)
        $coLocatedReports = $coLocatedQuery->orderByDesc('vote_score')
            ->paginate(4, ['*'], 'co_page')
            ->withQueryString()
            ->fragment('multi-issues');

        return view('reports.show', compact('report', 'userVote', 'coLocatedReports', 'totalCoLocatedCount', 'coFilter'));
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
     * Perbarui status laporan (oleh pembuat laporan atau admin).
     */
    public function updateStatus(Request $request, Report $report): JsonResponse|RedirectResponse
    {
        if (Auth::id() !== $report->user_id && ! Auth::user()?->isAdmin()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pembuat laporan atau administrator yang dapat mengubah status laporan ini.',
                ], 403);
            }

            abort(403, 'Hanya pembuat laporan atau administrator yang dapat mengubah status laporan ini.');
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
     * Hapus laporan (oleh pembuat laporan atau admin).
     */
    public function destroy(Request $request, Report $report): JsonResponse|RedirectResponse
    {
        if (Auth::id() !== $report->user_id && ! Auth::user()?->isAdmin()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki hak akses untuk menghapus laporan ini.',
                ], 403);
            }

            abort(403, 'Anda tidak memiliki hak akses untuk menghapus laporan ini.');
        }

        $report->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dihapus.',
            ]);
        }

        return redirect()->route('reports.index')->with('success', 'Laporan berhasil dihapus.');
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

    /**
     * Kirim notifikasi ke pengguna atau akun lembaga yang ditandai (@) dalam judul atau deskripsi laporan.
     */
    protected function dispatchReportMentionNotifications(Report $report): void
    {
        $sender = Auth::user();
        $senderUsername = $sender ? $sender->username : 'anon';
        $senderName = $sender ? $sender->name : 'Warga';
        $senderId = $sender ? $sender->id : null;

        $content = $report->title.' '.$report->description;

        if (preg_match_all('/@([a-zA-Z0-9_]{3,30})/', $content, $matches)) {
            $mentionedUsernames = array_unique($matches[1]);
            $mentionedUsers = User::whereIn('username', $mentionedUsernames)
                ->when($senderId, fn ($q) => $q->where('id', '!=', $senderId))
                ->whereRaw('LOWER(username) != ?', ['sira'])
                ->get();

            if ($mentionedUsers->isNotEmpty()) {
                Notification::send($mentionedUsers, new ReportMentionNotification(
                    senderUsername: $senderUsername,
                    senderName: $senderName,
                    reportId: $report->id,
                    reportTitle: $report->title,
                    snippet: Str::limit($report->description, 100),
                ));
            }
        }
    }
}
