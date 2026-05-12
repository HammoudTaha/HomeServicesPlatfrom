<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $table = 'wallet_transactions';
    protected $fillable = [
        'provider_wallet_id',
        'amount',
        'type',
        'notes',
        'payment_method'
    ];
    public function providerWallet()
    {
        return $this->belongsTo(ProviderWallet::class);
    }
}
