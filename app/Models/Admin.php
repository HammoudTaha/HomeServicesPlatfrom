<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;


class Admin extends Authenticatable
{
    protected $fillable = ['name', 'phone', 'password'];
    protected $hidden = ['password'];
    use HasFactory, Notifiable, HasApiTokens;
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function refreshTokens()
    {
        return $this->morphMany(RefreshToken::class, 'tokenable');
    }

}
