<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
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
            'syntax' => CarbonInterface::DIFF_ABSOLUTE,
        ]) ?? 'baru saja';
    }
}
