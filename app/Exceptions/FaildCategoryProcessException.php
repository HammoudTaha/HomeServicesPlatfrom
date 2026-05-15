<?php

namespace App\Exceptions;

use Exception;

class FaildCategoryProcessException extends BaseApiException
{
    public function __construct(string $message = "Failed to process category. Please try again later.", int $code = 500)
    {
        parent::__construct($code, $message);
    }
}
