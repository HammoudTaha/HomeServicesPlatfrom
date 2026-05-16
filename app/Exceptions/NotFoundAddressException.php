<?php

namespace App\Exceptions;

use Exception;

class NotFoundAddressException extends BaseApiException
{
    public function __construct()
    {
        parent::__construct(statusCode: 404, message: 'Address not found');
    }
}
