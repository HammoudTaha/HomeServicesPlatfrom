<?php

namespace App\DTOs;
use App\Http\Requests\Auth\VerifyPhoneRequest;
class VerifyPhoneDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(public string $phone, public string $code)
    {
    }
    public static function fromRequest(VerifyPhoneRequest $request)
    {
        return new self($request->phone, $request->code);
    }

}
