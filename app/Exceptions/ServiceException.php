<?php

namespace App\Exceptions;

use Exception;

class ServiceException extends Exception
{
    /**
     * Create a new ServiceException instance.
     *
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(string $message = "Service operation failed.", int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
