<?php

namespace Kernolab\Exception;

use Exception;

class MySqlPreparedStatementException extends Exception
{
    public function __construct($message, $code = 4, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}