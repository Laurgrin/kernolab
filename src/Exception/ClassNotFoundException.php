<?php

namespace Kernolab\Exception;

use Exception;

/**
 * Class ClassNotFoundException
 * @package Kernolab\Exception
 * @codeCoverageIgnore
 */
class ClassNotFoundException extends Exception
{
    public function __construct($message, $code = 1, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}