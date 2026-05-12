<?php

namespace App\Exceptions;

use Exception;

class FailedVerifyPhoneException extends BaseApiException
{
    public function __construct($message = 'Failed to verify phone number.Please try again later', $statusCode = 400)
    {
        parent::__construct($statusCode, $message, );
    }
}
