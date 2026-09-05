<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'user_id',
        'value',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'integer',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::saved(function (ReportVote $vote) {
            $vote->report?->recalculateVoteStatsAndTier();
        });

        static::deleted(function (ReportVote $vote) {
            $vote->report?->recalculateVoteStatsAndTier();
        });
    }
}
