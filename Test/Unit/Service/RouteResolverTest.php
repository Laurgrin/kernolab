<?php declare(strict_types = 1);

namespace Unit\Service;

use Kernolab\Exception\UndefinedRouteException;
use Kernolab\Routing\Request\Request;
use Kernolab\Service\Container;
use Kernolab\Service\RequestSanitizer;
use Kernolab\Service\RouteResolver;
use PHPUnit\Framework\TestCase;

class RouteResolverTest extends TestCase
{
    private $routeResolver;
    
    protected function setUp(): void
    {
        $this->routeResolver = new RouteResolver(new Container(), new RequestSanitizer());
    }
    
    protected function tearDown(): void
    {
        $this->routeResolver = null;
    }
    
    public function testResolve(): void
    {
        $requestUri = '/api/transaction/1';
        $requestMethod = 'put';
        
        $expected = new Request();
        $expected->setRequestUri('/api/transaction/1');
        $expected->setRequestMethod('put');
        $expected->setController('Transaction\Confirm');
        $expected->setRequestParams(['entity_id' => 1]);
        
        $this->assertEquals($expected, $this->routeResolver->resolve($requestUri, $requestMethod));
    }
    
    public function testResolveException(): void
    {
        $requestUri = '/api/transection/1';
        $requestMethod = 'put';
        
        $this->expectException(UndefinedRouteException::class);
        $this->routeResolver->resolve($requestUri, $requestMethod);
    }
    
    public function testGetRouteName(): void
    {
        $request = new Request();
        $request->setRequestMethod('mock')->setRequestUri('/mockery/1');
        $expected = 'MOCK/mockery/{id}';
        
        $this->assertEquals($expected, $this->routeResolver->getRouteName($request));
    }
    
    public function testGetRequestParams(): void
    {
        $request = new Request();
        $request->setRequestMethod('mock')->setRequestUri('/mockery/1');
        
        $expected = ['entity_id' => 1];
        
        $this->assertEquals($expected, $this->routeResolver->getRequestParams($request));
    }
}
