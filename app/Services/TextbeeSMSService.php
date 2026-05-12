<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;


class TextbeeSMSService
{

    public static function sendMessage(string $phone, string $message)
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => config('services.textbee.api_key'),
                'Content-Type' => 'application/json'
            ])->post(config('services.textbee.base_url') . config('services.textbee.device_id') . '/send-sms', [
                        'recipients' => [$phone],
                        'message' => $message
                    ]);
            if (!$response->successful()) {
                throw new \App\Exceptions\FailedSendOtpCodeException(message: 'Failed to send OTP code. Please try again later.');
            }
        } catch (\Exception) {
            throw new \App\Exceptions\FailedSendOtpCodeException(message: 'Failed to send OTP code. Please try again later.');
        }
    }
}
