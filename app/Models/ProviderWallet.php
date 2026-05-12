<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderWallet extends Model
{
    use HasFactory;

    protected $table = 'provider_wallets';
    protected $fillable = [
        'provider_id',
        'balance',
        'is_active',
    ];

    protected $hidden = [
        'is_active',
    ];
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }
}
