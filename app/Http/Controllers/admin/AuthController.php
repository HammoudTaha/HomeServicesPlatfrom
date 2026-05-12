<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private \App\Services\Auth\AdminAuthService $authService)
    {
    }
    public function login(\App\Http\Requests\Auth\LoginRequest $request)
    {
        $dto = \App\DTOs\LoginDTO::fromRequest($request);
        $result = $this->authService->login($dto);
        return \App\Traits\APIResponse::success(message: 'Admin login initiated successfully. Please verify the OTP sent to your phone to complete the login process.', data: $result);
    }


    public function verifyLogin(\App\Http\Requests\Auth\VerifyLoginRequest $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string',
            'login_token' => 'required|string',
        ]);
        $dto = \App\DTOs\VerifyLoginDTO::fromRequest($request);
        $result = $this->authService->verifyLogin($dto);
        return \App\Traits\APIResponse::success(message: 'Admin logged in successfully', data: $result);
    }
    public function changePassword(\App\Http\Requests\Admin\ChangePasswordRequest $request)
    {
        $dto = \App\DTOs\ChangePasswordDTO::fromRequest($request);
        $this->authService->changePassword($dto);
        return \App\Traits\APIResponse::success(message: 'Password updated successfully');
    }

    public function me(Request $request)
    {
        return \App\Traits\APIResponse::success(message: 'Admin details retrieved successfully', data: $request->user());
    }

    public function logout(Request $request)
    {
        $admin = $request->user();
        $this->authService->logout($admin);
        return \App\Traits\APIResponse::success(message: 'Admin logged out successfully');
    }

    public function refreshTokens(\App\Http\Requests\Auth\RefreshTokenRequest $request)
    {
        $tokens = $this->authService->refreshTokens($request->refresh_token);
        return \App\Traits\APIResponse::success(message: 'Tokens refreshed successfully', data: $tokens);
    }

}
