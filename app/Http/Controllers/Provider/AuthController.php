<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private \App\Services\Auth\ProviderAuthService $authService)
    {
    }

    public function login(\App\Http\Requests\Auth\LoginRequest $request)
    {
        $dto = \App\DTOs\LoginDTO::fromRequest($request);
        $result = $this->authService->login($dto);
        return \App\Traits\APIResponse::success(message: 'Provider logged in successfully', data: $result);
    }

    public function logout(Request $request)
    {
        $provider = $request->user();
        $this->authService->logout($provider);
        return \App\Traits\APIResponse::success(message: 'Provider logged out successfully');
    }

    public function refreshTokens(\App\Http\Requests\Auth\RefreshTokenRequest $request)
    {
        $tokens = $this->authService->refreshTokens($request->refresh_token);
        return \App\Traits\APIResponse::success(message: 'Tokens refreshed successfully', data: $tokens);
    }

    public function sendOtpCode(\App\Http\Requests\Auth\SendOtpRequest $request)
    {
        $this->authService->sendOtpCode($request->phone);
        return \App\Traits\APIResponse::success(message: 'OTP code sent successfully');
    }
    public function verifyPhoneForResetPassword(\App\Http\Requests\Auth\VerifyPhoneRequest $request)
    {
        $dto = \App\DTOs\VerifyPhoneDTO::fromRequest($request);
        $resetToken = $this->authService->verifyPhoneForResetPassword($dto);
        return \App\Traits\APIResponse::success(data: ['reset_token' => $resetToken], message: 'Phone number verified successfully. Use the reset token to reset your password.');
    }

    public function resetPassword(\App\Http\Requests\Auth\ResetPasswordRequest $request)
    {
        $dto = \App\DTOs\ResetPassowrdDTO::fromRequest($request);
        $this->authService->resetPassword($dto);
        return \App\Traits\APIResponse::success(message: 'Password reset successfully');
    }
}
