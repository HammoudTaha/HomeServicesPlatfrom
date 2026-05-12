<?php

namespace App\DTOs;

use App\Models\Admin;

class ChangePasswordDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(public Admin $admin, public string $currentPassword, public string $newPassword)
    {
    }
    public static function fromRequest(\App\Http\Requests\Admin\ChangePasswordRequest $request): self
    {
        return new self(
            admin: $request->user(),
            currentPassword: $request->input('current_password'),
            newPassword: $request->input('new_password'),
        );
    }
}
