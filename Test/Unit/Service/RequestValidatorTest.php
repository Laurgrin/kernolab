<?php declare(strict_types = 1);

namespace Unit\Service;

use Kernolab\Exception\RequestParameterException;
use Kernolab\Service\RequestValidator;
use PHPUnit\Framework\TestCase;

class RequestValidatorTest extends TestCase
{
    private $requestValidator;
    
    protected function setUp(): void
    {
        $this->requestValidator = new RequestValidator();
    }
    
    protected function tearDown(): void
    {
        $this->requestValidator = null;
    }
    
    public function testValidateRequest(): void
    {
        $input    = ['hello' => 'world'];
        $required = ['hello' => 'mars', 'goodbye' => 'world'];
        
        $this->expectException(RequestParameterException::class);
        $this->requestValidator->validateRequest($input, $required);
    }
}
