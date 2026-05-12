<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceOrder extends Model
{
    use HasFactory;

    protected $table = 'service_orders';
    protected $fillable = [
        'provider_id',
        'provider_service_id',
        'user_id',
        'title',
        'status',
        'address',
        'description'
    ];
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
    public function providerService()
    {
        return $this->belongsTo(ProviderService::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function images()
    {
        return $this->morphMany(Image::class, 'owner');
    }
}
