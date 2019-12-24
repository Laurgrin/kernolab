<?php
/**
 * Created by PhpStorm.
 * User: Laurynas
 * Date: 2019-12-24
 * Time: 15:59
 */
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
    
    public function testAddField()
    {
        $this->jsonResponse->addField("key", "value");
        $this->assertEquals('{"key":"value"}', $this->jsonResponse->getResponse());
    }
    
    public function testAddFieldMultiple()
    {
        $this->jsonResponse->addField("key", "value");
        $this->jsonResponse->addField("key2", "value2");
        $this->assertEquals('{"key":"value","key2":"value2"}', $this->jsonResponse->getResponse());
    }
    
    public function testAddFieldArray()
    {
        $this->jsonResponse->addField("key", ["value1", "value2", "value3"]);
        $this->assertEquals('{"key":["value1","value2","value3"]}', $this->jsonResponse->getResponse());
    }
    
    public function testAddError()
    {
        $this->jsonResponse->addError(500, "Error");
        $this->assertEquals('{"status":"error","errors":[{"code":500,"message":"Error"}]}',
            $this->jsonResponse->getResponse()
        );
    }
}
