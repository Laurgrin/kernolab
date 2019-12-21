<?php

namespace Test\Unit\Model\DataSource\MySql;

use Kernolab\Exception\UnknownOperandException;
use Kernolab\Model\DataSource\Criteria;
use Kernolab\Model\DataSource\MySql\CriteriaParser;
use PHPUnit\Framework\TestCase;

class CriteriaParserTest extends TestCase
{
    private $criteria;
    
    protected function setUp(): void
    {
        $this->criteria = new CriteriaParser("mock_table");
    }
    
    protected function tearDown(): void
    {
        $this->criteria = null;
    }
    
    /**
     * @dataProvider parseCriteriaProvider
     *
     * @param $input
     * @param $expected
     *
     * @throws \Kernolab\Exception\UnknownOperandException
     */
    public function testParseCriteria($input, $expected)
    {
        $result = $this->criteria->parseCriteria($input);
        
        $this->assertEquals($expected, $result);
    }
    
    public function testParseCriteriaException()
    {
        $input = [new Criteria("id", "ez", 3),];
        
        $this->expectException(UnknownOperandException::class);
        $this->criteria->parseCriteria($input);
    }
    
    public function parseCriteriaProvider()
    {
        return [
            "zero criteria" => [
                [],
                [
                    "query" => "SELECT * FROM `mock_table`",
                    "args"  => [],
                ]
            ],
            "one criteria" => [
                [
                    new Criteria("id", "eq", 3),
                ],
                [
                    "query" => "SELECT * FROM `mock_table` WHERE `id` = ?",
                    "args"  => ["id" => 3],
                ],
            ],
            "multiple criteria" => [
                [
                    new Criteria("id", "eq", 3),
                    new Criteria("sex", "eq", "male")
                ],
                [
                    "query" => "SELECT * FROM `mock_table` WHERE `id` = ? AND `sex` = ?",
                    "args"  => ["id" => 3, "sex" => "male"],
                ],
            ]
        ];
    }
}
