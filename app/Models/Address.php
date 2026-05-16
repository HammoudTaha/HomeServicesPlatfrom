<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Address extends Model
{
    use HasFactory;
    protected $table = 'addresses';
    protected $fillable = [
        'country',
        'city',
        'area',
        'street',
        'building',
        'floor',
        'apartment',
        'latitude',
        'longitude',
    ];
    public function owner()
    {
        return $this->morphTo();
    }
}
