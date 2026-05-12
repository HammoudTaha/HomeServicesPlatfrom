<?php

namespace App\Exceptions;


class NotFoundUserException extends BaseApiException
{
    public function __construct($message = 'No user found with the provided phone number.')
    {
        parent::__construct(404, $message);
    }
}
