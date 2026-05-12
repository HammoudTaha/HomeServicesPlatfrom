<?php

namespace App\Services\Auth;

use App\Models\OtpCode;
use App\Services\TextbeeSMSService;
use App\Exceptions\FailedSendOtpCodeException;
use App\Exceptions\FailedVerifyPhoneException;
use Carbon\CarbonInterval;
class OtpService
{
    public function __construct()
    {
    }
    public function sendOTP(string $phone)
    {
        $this->isBlocked($phone);
        $code = $code = random_int(100000, 999999) . '';
        $expiration = config('app.otp_expiration', 5);
        $expiresAt = now()->addMinutes((int) $expiration);
        OtpCode::updateOrCreate(
            [
                'phone' => $phone,
            ],
            [
                'code' => $code,
                'attempts' => 0,
                'expires_at' => $expiresAt,
                'blocked_until' => null
            ]
        );
        TextbeeSMSService::sendMessage($phone, "Your OTP code is: $code. It will expire in " . $expiration . " minutes.");
    }
    private function isBlocked(string $phone, string $message = 'You have been blocked from requesting OTP codes due to multiple failed attempts.')
    {
        $otpCode = OtpCode::where('phone', $phone)->first();
        if ($otpCode && $otpCode->isBlocked()) {
            $hours = now()->diffInHours($otpCode->blocked_until, false);
            $minutes = now()->diffInMinutes($otpCode->blocked_until, false);
            $seconds = now()->diffInSeconds($otpCode->blocked_until, false);
            $time = null;
            if ((int) $hours > 0) {
                $time = CarbonInterval::hours($hours)->cascade()->forHumans();
            } else if ((int) $minutes > 0) {
                $time = CarbonInterval::minutes($minutes)->cascade()->forHumans();
            } else {
                $time = CarbonInterval::seconds($seconds)->cascade()->forHumans();
            }
            throw new FailedSendOtpCodeException($message . " Please try again later after " . $time . ".");
        }
    }
    public function verify(string $phone, string $code)
    {
        $otpCode = OtpCode::where('phone', $phone)->first();
        if (!$otpCode) {
            throw new FailedVerifyPhoneException();
        }
        $this->isBlocked($phone, 'You have been blocked from verifying OTP codes due to multiple failed attempts.');
        if ($otpCode->code !== $code) {
            $otpCode->incrementAttempts();
            $attempts = $otpCode->attempts;
            if ($attempts < 5) {
                throw new FailedVerifyPhoneException(message: 'The provided OTP code does not match. Please try again .');
            }
            $otpCode->blocked_until = now()->addMinutes(60);
            $otpCode->save();
            throw new FailedVerifyPhoneException(message: 'The provided OTP code does not match. Please try again later.');
        }
        if ($otpCode->isExpired()) {
            throw new FailedVerifyPhoneException(message: 'The provided OTP code has expired. Please request a new one.');
        }
        return $otpCode;
    }
}
