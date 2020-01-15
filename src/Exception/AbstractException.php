<?php declare(strict_types = 1);

namespace Kernolab\Exception;

use Exception;

class AbstractException extends Exception
{
    public function __construct($message, $code = 1, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}