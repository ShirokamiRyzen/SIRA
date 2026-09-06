<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string|null $email
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin Notifiable
 */
#[Fillable(['name', 'username', 'email', 'password', 'is_admin', 'is_verified'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    /**
     * Determine if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin || $this->username === 'admin';
    }

    /**
     * Determine if the user is verified.
     */
    public function isVerified(): bool
    {
        return $this->isAdmin() || (bool) $this->is_verified;
    }

    /**
     * Get the badge type for the user: 'admin', 'verified', or null.
     */
    public function badgeType(): ?string
    {
        if ($this->isAdmin()) {
            return 'admin';
        }

        if ($this->is_verified) {
            return 'verified';
        }

        return null;
    }

    /**
     * Get the reports created by the user.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Get the votes cast by the user.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(ReportVote::class);
    }

    /**
     * Get the comments posted by the user.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(ReportComment::class);
    }
}
