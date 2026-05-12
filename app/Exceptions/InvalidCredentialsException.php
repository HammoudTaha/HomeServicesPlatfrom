<?php

namespace App\Exceptions;

use Exception;

class InvalidCredentialsException extends BaseApiException
{
    public function __construct(string $message = 'Invalid credentials provided.')
    {
        parent::__construct(401, $message);
    }
}
