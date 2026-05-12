<?php

namespace App\Exceptions;

use Exception;

class UserNotVerifiedException extends BaseApiException
{
    public function __construct($message = 'Your phone number is not verified. Please verify your phone number to proceed.', $statusCode = 403)
    {
        parent::__construct($statusCode, $message);
    }
}
