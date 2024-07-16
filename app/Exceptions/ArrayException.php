<?php

namespace App\Exceptions;

use Exception;

class ArrayException extends Exception
{
    public function __construct(array $message = null, int $code = 0, Exception $previous = null) {
        parent::__construct(json_encode($message), $code, $previous);
    }

    public function getMessages($assoc = false) {
        return json_decode($this->getMessage(), $assoc);
    }
}
