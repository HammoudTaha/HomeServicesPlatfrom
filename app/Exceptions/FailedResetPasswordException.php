<?php

namespace App\Exceptions;

use Exception;

class FailedResetPasswordException extends BaseApiException
{
    public function __construct(string $message = 'Failed to reset password. Invalid or expired reset token.')
    {
        parent::__construct(400, $message);
    }
}
