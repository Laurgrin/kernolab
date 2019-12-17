<?php

require_once(__DIR__ . "/../Exception/ClassNotFoundException.php");

spl_autoload_register(function ($className) {
    $fileName = __DIR__ . "/../" . $className . ".php";
    
    if (is_readable($fileName)) {
        require_once($fileName);
    } else {
     throw new \Exception\ClassNotFoundException(
         "Autoloader could not find the class [$className] in [$fileName]"
     );
    }
});