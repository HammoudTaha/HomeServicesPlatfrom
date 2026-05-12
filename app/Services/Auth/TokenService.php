<?php

namespace App\Services\Auth;
use Carbon\CarbonInterval;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Models\RefreshToken;

class TokenService
{
    private int $accessTokenInterval;
    private int $refreshTokenInterval;
    public function __construct()
    {
        $this->accessTokenInterval = config('sanctum.access_token_expiration', 15);
        $this->refreshTokenInterval = config('sanctum.refresh_token_expiration', 30);
    }
    public function issueTokens(Model $user)
    {
        $accessToken = $user->createToken('access_token')->plainTextToken;
        $refreshToken = Str::random(64);
        $hashedRefreshToken = hash('sha256', $refreshToken);
        $user->refreshTokens()->create([
            'token_hash' => $hashedRefreshToken,
            'expires_at' => now()->addMinutes($this->refreshTokenInterval),
        ]);
        $accessTokenIntervalText = CarbonInterval::minutes($this->accessTokenInterval)->cascade()->forHumans();
        $refreshTokenIntervalText = CarbonInterval::minutes($this->refreshTokenInterval)->cascade()->forHumans();
        return [
            'access_token' => ['token' => $accessToken, 'life_time' => $this->accessTokenInterval, 'life_time_plain_text' => $accessTokenIntervalText],
            'refresh_token' => ['token' => $refreshToken, 'life_time' => $this->refreshTokenInterval, 'life_time_plain_text' => $refreshTokenIntervalText],
            'token_type' => 'Bearer'
        ];
    }

    public function rotateRefreshToken(string $refreshToken)
    {
        $hashedRefreshToken = hash('sha256', $refreshToken);
        $storedToken = RefreshToken::where('token_hash', $hashedRefreshToken)->first();
        if (!$storedToken || !$storedToken->isValid()) {
            throw new \App\Exceptions\InvalidRefreshTokenException();
        }
        $tokenable = $storedToken->tokenable;
        $storedToken->revoke();
        $storedToken->delete();
        return $this->issueTokens($tokenable);
    }

    public function revokeTokens(Model $user)
    {
        $user->tokens()->delete();
        $user->refreshTokens()->delete();
    }
}
