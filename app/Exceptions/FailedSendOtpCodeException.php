<?php

namespace App\Exceptions;

use Exception;

class FailedSendOtpCodeException extends BaseApiException
{

    public function __construct($message = 'Failed to send OTP code. Please try again later.', $statusCode = 500)
    {
        parent::__construct($statusCode, $message, );
    }
}
