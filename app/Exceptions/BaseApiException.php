<?php

namespace App\Exceptions;

use Exception;

class BaseApiException extends Exception
{
    public function __construct(public int $statusCode = 500, string $message = 'Something went wrong')
    {
        parent::__construct($message);
    }
}
