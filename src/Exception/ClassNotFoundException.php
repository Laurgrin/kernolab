<?php
/**
 * Created by PhpStorm.
 * User: Laurynas
 * Date: 2019-12-17
 * Time: 15:55
 */

namespace Kernolab\Exception;

use Exception;

class ClassNotFoundException extends Exception
{
    public function __construct($message, $code = 1, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}