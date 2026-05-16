<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
        'is_verified',
        'is_active'
    ];
    protected $hidden = [
        'password',
        'is_verified',
        'is_active'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
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
    public function serviceOrders()
    {
        return $this->hasMany(ServiceOrder::class);
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'owner');
    }
    public function address()
    {
        return $this->morphOne(Address::class, 'owner');
    }

}
