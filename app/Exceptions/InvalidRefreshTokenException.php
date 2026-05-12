<?php

namespace App\Exceptions;

use Exception;
use Faker\Provider\Base;

class InvalidRefreshTokenException extends BaseApiException
{
    public function __construct(string $message = 'The provided refresh token is invalid or has expired. Please log in again to obtain a new refresh token.', int $code = 401)
    {
        parent::__construct($code, $message, );
    }
}
