<?php

namespace Kernolab\Exception;

use Exception;

/**
 * Class MySqlConnectionException
 * @package Kernolab\Exception
 * @codeCoverageIgnore
 */
class MySqlConnectionException extends Exception
{
    public function __construct($message, $code = 2, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}