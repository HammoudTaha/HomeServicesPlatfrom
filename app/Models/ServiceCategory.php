<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $table = 'service_categories';
    protected $fillable = [
        'name',
        'slug',
        'commission',
        'is_active'
    ];
    public function providers(): HasMany
    {
        return $this->hasMany(Provider::class, 'service_category_id');
    }
    public function image()
    {
        return $this->morphOne(Image::class, 'owner');
    }

}
