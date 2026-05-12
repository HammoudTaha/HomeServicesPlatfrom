<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Image extends Model
{
    use HasFactory;

    protected $table = 'images';
    protected $fillable = [
        'image_url',
        'public_id',
    ];
    public function owner()
    {
        return $this->morphTo();
    }


}
