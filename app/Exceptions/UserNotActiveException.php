<?php

namespace App\Exceptions;

use Exception;
use Faker\Provider\Base;

class UserNotActiveException extends BaseApiException
{
    public function __construct(string $message = "Your account is not active . Please contact support to activate your account", int $code = 403, ?Exception $previous = null)
    {
        parent::__construct($code, $message);
    }
}
