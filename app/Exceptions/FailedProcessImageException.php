<?php

namespace App\Exceptions;

class FailedProcessImageException extends BaseApiException
{
    public function __construct($message = "Failed to upload the image. Please try again later.", $code = 500)
    {
        parent::__construct($code, $message);
    }
}
