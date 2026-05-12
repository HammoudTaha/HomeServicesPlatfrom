<?php

namespace App\DTOs;

use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\UploadedFile;
class RegisterDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(public string $firstName, public string $lastName, public string $phone, public string $password, public ?string $email = null, public ?UploadedFile $image = null)
    {
    }
    public static function fromRequest(RegisterRequest $request)
    {
        return new self($request->first_name, $request->last_name, $request->phone, $request->password, $request->email, $request->image);
    }
}
