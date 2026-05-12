<?php

namespace App\DTOs;
use App\Http\Requests\Auth\VerifyLoginRequest;
class VerifyLoginDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(public string $phone, public string $code, public string $loginToken)
    {
    }
    public static function fromRequest(VerifyLoginRequest $request): self
    {
        return new self(
            phone: $request->phone,
            code: $request->code,
            loginToken: $request->login_token
        );
    }
}
