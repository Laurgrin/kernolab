<?php

namespace Test\Unit\Model\DataSource\MySql;

use Model\DataSource\MySql\Criteria;
use PHPUnit\Framework\TestCase;

class CriteriaTest extends TestCase
{
    private $criteria;
    
    protected function setUp(): void
    {
        $this->criteria = new Criteria("mock_table");
    }
    
    protected function tearDown(): void
    {
        $this->criteria = null;
    }
    
    public function testParseCriteria()
    {
        $input = [
            [
                "field"   => "id",
                "operand" => "eq",
                "value"   => 3,
            ],
        ];
        
        $expected = [
            "query" => "SELECT * FROM `mock_table` WHERE ``",
            "args"  => ["id" => 3],
        ];
        
        $result = $this->criteria->parseCriteria($input);
        
        $this->assertEquals($expected, $result);
    }
}
