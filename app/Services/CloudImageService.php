<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;

class CloudImageService
{

    public static function upload(UploadedFile $file)
    {
        $response = Http::attach(
            'file',
            file_get_contents($file->getRealPath()),
            $file->getClientOriginalName()
        )->post('https://api.cloudinary.com/v1_1/djsiq4wxb/image/upload', [
                    'upload_preset' => 'home_services',
                    'folder' => 'Home Services',
                ]);
        if ($response->successful()) {
            return ['url' => $response->json()['secure_url'], 'public_id' => $response->json()['public_id']];
        } else {
            throw new \App\Exceptions\FailedProcessImageException();
        }
    }
    public static function delete(string $publicId)
    {
        $timestamp = time();
        $secrtKey = config('cloudinary.secret-key');
        $signature = sha1(
            "public_id={$publicId}&timestamp={$timestamp}{$secrtKey}"
        );
        $response = Http::delete('https://api.cloudinary.com/v1_1/djsiq4wxb/image/destroy', [
            'public_id' => $publicId,
            'api_key' => config('cloudinary.api-key'),
            'signature' => $signature,
            'timestamp' => $timestamp,
        ]);
        if (!$response->successful()) {
            throw new \App\Exceptions\FailedProcessImageException();
        }
    }

    public static function update(UploadedFile $file, string $publicId)
    {
        self::delete($publicId);
        return self::upload($file);
    }
}
