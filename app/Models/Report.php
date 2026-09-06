<?php

namespace App\Models;

use Carbon\Constants\DiffOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    public const CATEGORIES = [
        'kebakaran' => [
            'key' => 'kebakaran',
            'label' => 'Kebakaran',
            'icon' => 'fire',
            'symbol' => 'fire',
            'color' => '#ef4444',
            'badge_class' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border-rose-200 dark:border-rose-900/50',
        ],
        'infrastruktur' => [
            'key' => 'infrastruktur',
            'label' => 'Infrastruktur Rusak',
            'icon' => 'wrench',
            'symbol' => 'wrench',
            'color' => '#f97316',
            'badge_class' => 'bg-orange-100 text-orange-800 dark:bg-orange-950/60 dark:text-orange-300 border-orange-200 dark:border-orange-900/50',
        ],
        'bencana_alam' => [
            'key' => 'bencana_alam',
            'label' => 'Bencana Alam',
            'icon' => 'cloud',
            'symbol' => 'cloud',
            'color' => '#0284c7',
            'badge_class' => 'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300 border-sky-200 dark:border-sky-900/50',
        ],
        'kelistrikan' => [
            'key' => 'kelistrikan',
            'label' => 'Lampu & Kelistrikan',
            'icon' => 'bolt',
            'symbol' => 'bolt',
            'color' => '#eab308',
            'badge_class' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-900/50',
        ],
        'lingkungan' => [
            'key' => 'lingkungan',
            'label' => 'Sampah & Lingkungan',
            'icon' => 'trash',
            'symbol' => 'trash',
            'color' => '#16a34a',
            'badge_class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-900/50',
        ],
        'fasilitas_umum' => [
            'key' => 'fasilitas_umum',
            'label' => 'Fasilitas Umum',
            'icon' => 'building-office',
            'symbol' => 'building-office',
            'color' => '#8b5cf6',
            'badge_class' => 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border-purple-200 dark:border-purple-900/50',
        ],
        'lainnya' => [
            'key' => 'lainnya',
            'label' => 'Lainnya',
            'icon' => 'tag',
            'symbol' => 'tag',
            'color' => '#64748b',
            'badge_class' => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700',
        ],
    ];

    protected $fillable = [
        'user_id',
        'title',
        'category',
        'description',
        'image_base64',
        'latitude',
        'longitude',
        'geohash',
        'province',
        'city',
        'district',
        'subdistrict',
        'formatted_address',
        'osm_place_id',
        'rank_tier',
        'upvotes_count',
        'downvotes_count',
        'vote_score',
        'comments_count',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'upvotes_count' => 'integer',
            'downvotes_count' => 'integer',
            'vote_score' => 'integer',
            'comments_count' => 'integer',
        ];
    }

    /**
     * Relasi ke pembuat laporan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke vote pada laporan ini.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(ReportVote::class);
    }

    /**
     * Relasi ke komentar utama (tanpa parent).
     */
    public function rootComments(): HasMany
    {
        return $this->hasMany(ReportComment::class)->whereNull('parent_id')->latest();
    }

    /**
     * Seluruh komentar pada laporan ini.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(ReportComment::class);
    }

    /**
     * Ambil vote dari user tertentu (jika ada).
     */
    public function userVote(?User $user): ?ReportVote
    {
        if (! $user) {
            return null;
        }

        if ($this->relationLoaded('votes')) {
            return $this->votes->firstWhere('user_id', $user->id);
        }

        return $this->votes()->where('user_id', $user->id)->first();
    }

    /**
     * Hitung ulang upvotes, downvotes, vote_score, dan tentukan rank_tier otomatis.
     */
    public function recalculateVoteStatsAndTier(): void
    {
        $upvotes = $this->votes()->where('value', 1)->count();
        $downvotes = $this->votes()->where('value', -1)->count();
        $score = $upvotes - $downvotes;

        $tier = match (true) {
            $score >= 100 => 'critical',
            $score >= 50 => 'urgent',
            $score >= 10 => 'trending',
            default => 'normal',
        };

        $this->update([
            'upvotes_count' => $upvotes,
            'downvotes_count' => $downvotes,
            'vote_score' => $score,
            'rank_tier' => $tier,
        ]);
    }

    /**
     * Dapatkan durasi waktu sejak laporan pertama kali diunggah dalam format Bahasa Indonesia.
     * Digunakan untuk menampilkan badge lama laporan belum diproses.
     */
    public function getPendingDurationAttribute(): string
    {
        return $this->created_at?->locale('id')->diffForHumans([
            'syntax' => DiffOptions::DIFF_ABSOLUTE,
        ]) ?? 'baru saja';
    }

    /**
     * Dapatkan metadata kategori laporan (label, icon, color, symbol, badge_class).
     *
     * @return array<string, string>
     */
    public function getCategoryMetaAttribute(): array
    {
        $cat = $this->category ?: 'infrastruktur';

        return self::CATEGORIES[$cat] ?? self::CATEGORIES['lainnya'];
    }

    public function getCategoryLabelAttribute(): string
    {
        return $this->category_meta['label'];
    }

    public function getCategoryIconAttribute(): string
    {
        return $this->category_meta['icon'];
    }

    public function getCategoryColorAttribute(): string
    {
        return $this->category_meta['color'];
    }

    public function getCategorySymbolAttribute(): string
    {
        return $this->category_meta['symbol'];
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'resolved' => 'Selesai',
            'in_progress' => 'Sedang Diproses',
            'archived' => 'Diarsipkan',
            default => 'Menunggu Respon',
        };
    }

    public function getTierLabelAttribute(): string
    {
        return match ($this->rank_tier) {
            'critical' => 'Prioritas Kritis',
            'urgent' => 'Prioritas Mendesak',
            'trending' => 'Trending',
            default => 'Reguler',
        };
    }

    public function getLocationShortAttribute(): string
    {
        if ($this->district && $this->city) {
            if ($this->district === $this->city) {
                return $this->city;
            }

            return "{$this->district}, {$this->city}";
        }

        return $this->city ?? ($this->district ?? 'Indonesia');
    }

    public function getOgMetaDescriptionAttribute(): string
    {
        $cleanDesc = trim(preg_replace('/\s+/', ' ', strip_tags($this->description)));
        $status = "Status: {$this->status_label}";
        $location = "Lokasi: {$this->location_short}";
        $category = "Kategori: {$this->category_label}";
        $votes = "Dukungan: +{$this->vote_score} Suara";
        $comments = "Komentar: {$this->comments_count} Tanggapan";
        $reporter = 'Pelapor: @'.($this->user->username ?? 'anon');

        return "{$cleanDesc} — [{$status} | {$location} | {$category} | {$votes} | {$comments} | {$reporter}]";
    }

    /**
     * Scope query untuk menambahkan counter laporan se-lokasi secara efisien tanpa N+1.
     */
    public function scopeWithMultiIssueStatus($query)
    {
        return $query->select('reports.*')
            ->selectRaw(
                '(SELECT COUNT(*) FROM reports r2 WHERE r2.latitude = reports.latitude AND r2.longitude = reports.longitude AND r2.id != reports.id AND r2.deleted_at IS NULL) as co_located_reports_count'
            );
    }

    /**
     * Scope query untuk memfilter laporan yang berada di titik multi-masalah (terdapat laporan lain di titik yang sama).
     */
    public function scopeOnlyMultiIssue($query)
    {
        return $query->whereRaw(
            '(SELECT COUNT(*) FROM reports r2 WHERE r2.latitude = reports.latitude AND r2.longitude = reports.longitude AND r2.id != reports.id AND r2.deleted_at IS NULL) > 0'
        );
    }

    /**
     * Scope query untuk memfilter laporan tunggal (tidak ada laporan lain di titik yang sama).
     */
    public function scopeOnlySingleIssue($query)
    {
        return $query->whereRaw(
            '(SELECT COUNT(*) FROM reports r2 WHERE r2.latitude = reports.latitude AND r2.longitude = reports.longitude AND r2.id != reports.id AND r2.deleted_at IS NULL) = 0'
        );
    }

    /**
     * Hitung jumlah laporan lain yang berada di titik lokasi/koordinat yang sama.
     */
    public function getCoLocatedReportsCountAttribute(): int
    {
        if (array_key_exists('co_located_reports_count', $this->attributes)) {
            return (int) $this->attributes['co_located_reports_count'];
        }

        if (! $this->latitude || ! $this->longitude) {
            return 0;
        }

        static $cache = [];
        $key = ((string) $this->latitude).':'.((string) $this->longitude);

        if (! array_key_exists($key, $cache)) {
            $cache[$key] = static::where('id', '!=', $this->id)
                ->where('latitude', $this->latitude)
                ->where('longitude', $this->longitude)
                ->count();
        }

        return $cache[$key];
    }

    /**
     * Menentukan apakah laporan berada di titik lokasi yang terdeteksi memiliki multi-masalah.
     */
    public function getIsMultiIssueAttribute(): bool
    {
        return $this->co_located_reports_count > 0;
    }

    /**
     * Total seluruh masalah yang tercatat pada titik lokasi/koordinat yang sama.
     */
    public function getTotalLocationIssuesAttribute(): int
    {
        return $this->co_located_reports_count + 1;
    }
}
