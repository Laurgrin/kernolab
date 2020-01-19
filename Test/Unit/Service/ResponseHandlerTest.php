<?php declare(strict_types = 1);
namespace Unit\Service;

use Kernolab\Controller\JsonResponse;
use Kernolab\Service\ResponseHandler;
use PHPUnit\Framework\TestCase;

class ResponseHandlerTest extends TestCase
{
    private $responseHandler;
    
    protected function setUp(): void
    {
        $this->responseHandler = $this->getMockBuilder(ResponseHandler::class)
                                      ->disableOriginalConstructor()
                                      ->setMethodsExcept(['handleResponse'])
                                      ->getMock();
    }
    
    protected function tearDown(): void
    {
        $this->responseHandler = null;
    }
    
    public function testHandleResponse(): void
    {
        $response = new JsonResponse();
        $response->addField('hello', 'world');
        
        $expected = '{"hello":"world"}';
        
        $this->expectOutputString($expected);
        $this->responseHandler->handleResponse($response);
    }
}
