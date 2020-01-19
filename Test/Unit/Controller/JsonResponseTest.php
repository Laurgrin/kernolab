<?php declare(strict_types = 1);

namespace Test\Unit\Controller;

use Kernolab\Controller\JsonResponse;
use PHPUnit\Framework\TestCase;

class JsonResponseTest extends TestCase
{
    private $jsonResponse;
    
    protected function setUp(): void
    {
        $this->jsonResponse = new JsonResponse();
    }
    
    protected function tearDown(): void
    {
        $this->jsonResponse = null;
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testAddField(): void
    {
        $this->jsonResponse->addField('key', 'value');
        $this->assertEquals('{"key":"value"}', $this->jsonResponse->getResponse());
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testAddFieldMultiple(): void
    {
        $this->jsonResponse->addField('key', 'value');
        $this->jsonResponse->addField('key2', 'value2');
        $this->assertEquals('{"key":"value","key2":"value2"}', $this->jsonResponse->getResponse());
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testAddFieldArray(): void
    {
        $this->jsonResponse->addField('key', ['value1', 'value2', 'value3']);
        $this->assertEquals('{"key":["value1","value2","value3"]}', $this->jsonResponse->getResponse());
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testAddError(): void
    {
        $this->jsonResponse->addError(500, 'Error');
        $this->assertEquals('{"status":"error","code":500,"message":"Error"}',
            $this->jsonResponse->getResponse()
        );
    }
}
