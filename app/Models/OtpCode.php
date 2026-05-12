<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $table = 'otp_codes';
    protected $fillable = ['phone', 'code', 'expires_at', 'attempts', 'blocked_until'];
    protected $casts = [
        'expires_at' => 'datetime',
        'blocked_until' => 'datetime',
    ];

    public function isExpired()
    {
        return $this->expires_at?->isPast() ?? true;
    }

    public function isBlocked()
    {
        return $this->blocked_until && $this->blocked_until->isFuture();
    }

    public function isVerified()
    {
        return $this->isExpired() && !$this->isBlocked();
    }

    public function isBlockedOrExpired()
    {
        return $this->isBlocked() || $this->isExpired();
    }

    public function incrementAttempts()
    {
        $this->attempts++;
        $this->save();
    }


}
