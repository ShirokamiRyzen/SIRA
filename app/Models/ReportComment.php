<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'report_id',
        'user_id',
        'parent_id',
        'content',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ReportComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ReportComment::class, 'parent_id')->with(['user', 'replies'])->oldest();
    }

    protected static function booted(): void
    {
        static::created(function (ReportComment $comment) {
            $comment->report?->increment('comments_count');
        });

        static::deleted(function (ReportComment $comment) {
            $comment->report?->decrement('comments_count');
        });
    }
}
