<?php

namespace Test\Unit\Model\DataSource\MySql;

use Kernolab\Model\DataSource\MySql\Criteria;
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
                    [
                        "field"   => "id",
                        "operand" => "eq",
                        "value"   => 3,
                    ],
                ],
                [
                    "query" => "SELECT * FROM `mock_table` WHERE `id` = ?",
                    "args"  => ["id" => 3],
                ],
            ],
            "multiple criteria" => [
                [
                    [
                        "field"   => "id",
                        "operand" => "eq",
                        "value"   => 3,
                    ],
                    [
                        "field"   => "sex",
                        "operand" => "eq",
                        "value"   => "male",
                    ]
                ],
                [
                    "query" => "SELECT * FROM `mock_table` WHERE `id` = ? AND `sex` = ?",
                    "args"  => ["id" => 3, "sex" => "male"],
                ],
            ]
        ];
    }
}
