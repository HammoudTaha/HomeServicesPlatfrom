<?php

namespace App\Exceptions;

use Exception;

class UserAlreadyExistsException extends BaseApiException
{
    public function __construct()
    {
        parent::__construct(400, 'User with this phone number already exists.');
    }
}
