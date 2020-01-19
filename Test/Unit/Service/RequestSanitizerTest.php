<?php declare(strict_types = 1);

namespace Unit\Service;

use Kernolab\Service\RequestSanitizer;
use PHPUnit\Framework\TestCase;

class RequestSanitizerTest extends TestCase
{
    
    private $requestSanitizer;
    
    protected function setUp(): void
    {
        $this->requestSanitizer = new RequestSanitizer();
    }
    
    protected function tearDown(): void
    {
        $this->requestSanitizer = null;
    }
    
    public function testSanitize()
    {
        $input    = ['hoho' => '<>#$%hello/\a\dWorld'];
        $expected = ['hoho' => '#$%hello/\a\dWorld'];
        $actual = $this->requestSanitizer->sanitize($input);
        
        $this->assertEquals($expected, $actual);
    }
}
