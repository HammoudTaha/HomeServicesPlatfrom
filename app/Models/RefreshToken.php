<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class RefreshToken extends Model
{
    protected $casts = [
        'expires_at' => 'datetime',
        'revoked' => 'boolean'
    ];
    protected $fillable = ['token_hash', 'expires_at', 'revoked'];

    public function tokenable()
    {
        return $this->morphTo();
    }
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
    public function isRevoked()
    {
        return $this->revoked;
    }
    public function revoke()
    {
        $this->update(['revoked', true]);
    }
    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isRevoked();
    }
}
