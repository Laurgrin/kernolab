<?php

namespace Exception;

use Exception;

class UnknownOperandException extends Exception
{
    public function __construct($message, $code = 5, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}