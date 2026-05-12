<?php

namespace App\Exceptions;

use Exception;

class AlreadyVerifiedException extends BaseApiException
{
    public function __construct($message = 'This phone number is already verified.', $statusCode = 400)
    {
        parent::__construct($statusCode, $message, );
    }
}
