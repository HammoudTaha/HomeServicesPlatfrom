<?php

namespace App\DTOs;

use App\Http\Requests\Auth\LoginRequest;

class LoginDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(public string $phone, public string $password)
    {
    }
    public static function fromRequest(LoginRequest $request)
    {
        return new self($request->phone, $request->password);
    }
}
