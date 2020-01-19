<?php declare(strict_types = 1);
namespace Unit\Service;

use Kernolab\Exception\ContainerException;
use Kernolab\Model\Entity\Transaction\Transaction;
use Kernolab\Service\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    private $container;
    
    protected function setUp(): void
    {
        $this->container = new Container();
    }
    
    protected function tearDown(): void
    {
        $this->container = null;
    }
    
    public function testGet(): void
    {
        $object = $this->container->get(Transaction::class);
        $this->assertInstanceOf(Transaction::class, $object);
    }
    
    public function testGetException(): void
    {
        $this->expectException(\ReflectionException::class);
        $object = $this->container->get(Transactione::class);
    }
}
