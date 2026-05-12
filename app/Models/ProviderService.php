<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProviderService extends Model
{
    use HasFactory;

    protected $table = 'provider_services';
    protected $fillable = [
        'provider_id',
        'title',
        'description',
        'price',
        'is_active',
    ];

    protected $hidden = [
        'is_active',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
    public function image()
    {
        return $this->morphOne(Image::class, 'owner');
    }
}
