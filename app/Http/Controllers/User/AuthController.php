<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use App\Http\Resources\AddressResource;
class AuthController extends Controller
{
    public function __construct(private \App\Services\Auth\UserAuthService $authService)
    {
    }

    public function register(\App\Http\Requests\Auth\RegisterRequest $request)
    {
        $dto = \App\DTOs\RegisterDTO::fromRequest($request);
        $this->authService->register($dto);
        return ApiResponse::success(message: 'User registered successfully. Please verify your phone number to complete the registration process.');
    }
    public function verifyPhone(\App\Http\Requests\Auth\VerifyPhoneRequest $request)
    {
        $dto = \App\DTOs\VerifyPhoneDTO::fromRequest($request);
        $data = $this->authService->verifyPhone($dto);
        return ApiResponse::success(message: 'Phone number verified successfully', data: $data);
    }

    public function verifyPhoneForResetPassword(\App\Http\Requests\Auth\VerifyPhoneRequest $request)
    {
        $dto = \App\DTOs\VerifyPhoneDTO::fromRequest($request);
        $resetToken = $this->authService->verifyPhoneForResetPassword($dto);
        return ApiResponse::success(data: ['reset_token' => $resetToken], message: 'Phone number verified successfully. Use the reset token to reset your password.');
    }

    public function login(\App\Http\Requests\Auth\LoginRequest $request)
    {
        $dto = \App\DTOs\LoginDTO::fromRequest($request);
        $result = $this->authService->login($dto);
        return ApiResponse::success(message: 'User logged in successfully', data: $result);
    }
    public function resetPassword(\App\Http\Requests\Auth\ResetPasswordRequest $request)
    {
        $dto = \App\DTOs\ResetPassowrdDTO::fromRequest($request);
        $this->authService->resetPassword($dto);
        return ApiResponse::success(message: 'Password reset successfully');
    }

    public function sendOtpCode(\App\Http\Requests\Auth\SendOtpRequest $request)
    {
        $this->authService->sendOtpCode($request->phone);
        return ApiResponse::success(message: 'OTP code sent successfully');
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $this->authService->logout($user);
        return ApiResponse::success(message: 'User logged out successfully');
    }


    public function refreshTokens(\App\Http\Requests\Auth\RefreshTokenRequest $request)
    {
        $tokens = $this->authService->refreshTokens($request->refresh_token);
        return ApiResponse::success(message: 'Tokens refreshed successfully', data: $tokens, );
    }
    public function me(Request $request)
    {
        return ApiResponse::success(message: 'User profile retrieved successfully', data: new UserResource(
            $request->user()->load('image')
        ));
    }

    public function addAddress(\App\Http\Requests\Auth\AddAddressRequest $request)
    {
        $user = $request->user();
        $dto = \App\DTOs\AddressDTO::fromRequest($request);
        $address = $this->authService->addAddress($dto, $user);
        return ApiResponse::success(message: 'Address added successfully', data: new AddressResource($address));
    }

    public function updateAddress(\App\Http\Requests\Auth\UpdateAddressRequest $request)
    {
        $user = $request->user();
        $dto = \App\DTOs\AddressDTO::fromRequest($request);
        $address = $this->authService->updateAddress($dto, $user);
        return ApiResponse::success(message: 'Address updated successfully', data: new AddressResource($address));
    }
}
