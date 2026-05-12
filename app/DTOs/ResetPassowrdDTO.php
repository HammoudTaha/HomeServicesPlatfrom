<?php

namespace App\DTOs;

use App\Http\Requests\Auth\ResetPasswordRequest;

class ResetPassowrdDTO
{
    public function __construct(public string $phone, public string $newPassword, public string $resetToken)
    {
    }
    public static function fromRequest(ResetPasswordRequest $request)
    {
        return new self($request->phone, $request->new_password, $request->reset_token);
    }

}
