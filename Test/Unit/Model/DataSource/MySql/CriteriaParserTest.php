<?php

namespace Test\Unit\Model\DataSource\MySql;

use Kernolab\Exception\UnknownOperandException;
use Kernolab\Model\DataSource\Criteria;
use Kernolab\Model\DataSource\MySql\QueryGenerator;
use PHPUnit\Framework\TestCase;

class CriteriaParserTest extends TestCase
{
    private $criteria;
    
    protected function setUp(): void
    {
        $this->criteria = new QueryGenerator();
    }
    
    protected function tearDown(): void
    {
        $this->criteria = null;
    }
    
    /**
     * @dataProvider parseCriteriaProvider
     *
     * @param $target
     * @param $input
     * @param $expected
     *
     * @throws \Kernolab\Exception\UnknownOperandException
     */
    public function testParseCriteria($target, $input, $expected)
    {
        $result = $this->criteria->parseRetrieval($target, $input);
        
        $this->assertEquals($expected, $result);
    }
    
    public function testParseCriteriaException()
    {
        $input = [new Criteria("id", "ez", 3),];
        
        $this->expectException(UnknownOperandException::class);
        $this->criteria->parseRetrieval( "mock_table", $input);
    }
    
    public function parseCriteriaProvider()
    {
        return [
            "zero criteria" => [
                "mock_table",
                [],
                [
                    "query" => "SELECT * FROM `mock_table`",
                    "args"  => [],
                ]
            ],
            "one criteria" => [
                "mock_table",
                [
                    new Criteria("id", "eq", 3),
                ],
                [
                    "query" => "SELECT * FROM `mock_table` WHERE `id` = ?",
                    "args"  => ["id" => 3],
                ],
            ],
            "multiple criteria" => [
                "mock_table",
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
    
    /**
     * @dataProvider parseInsertionProvider
     * @param $target
     * @param $columns
     * @param $skipEntityId
     * @param $expected
     */
    public function testParseInsertion($target, $columns, $skipEntityId, $expected)
    {
        $result = $this->criteria->parseInsertion($target, $columns, $skipEntityId);
        
        $this->assertEquals($expected, $result);
    }
    
    public function parseInsertionProvider()
    {
        return [
            [
                "mock_table",
                [
                    "col1",
                    "col2"
                ],
                false,
                "INSERT INTO `mock_table` (`col1`, `col2`) VALUES (?, ?)" .
                " ON DUPLICATE KEY UPDATE `col1` = VALUES(`col1`), `col2` = VALUES(`col2`)"
            ],
            [
                "mock_table",
                [
                    "col1",
                    "col2"
                ],
                true,
                "INSERT INTO `mock_table` (`col2`) VALUES (?)" .
                " ON DUPLICATE KEY UPDATE `col2` = VALUES(`col2`)"
            ]
        ];
    }
}
