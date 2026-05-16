<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class Provider extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'password',
        'is_active',
        'email',
        'description',
        'experience_years',
        'rating',
        'service_category_id',
        'is_available',
        'rating_count',
    ];
    protected $hidden = [
        'password',
        'is_active',
    ];
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isSetPassword()
    {
        return !is_null($this->password);
    }

    public function refreshTokens()
    {
        return $this->morphMany(RefreshToken::class, 'tokenable');
    }

    public function services()
    {
        return $this->hasMany(ProviderService::class);
    }

    public function wallet()
    {
        return $this->hasOne(ProviderWallet::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
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
