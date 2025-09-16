<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ConcurrencyException extends Exception
{
    public function __construct($message = "Concurrency conflict: The resource has been modified by another user.", $code = 409, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
