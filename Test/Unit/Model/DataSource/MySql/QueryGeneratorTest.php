<?php declare(strict_types = 1);

namespace Test\Unit\Model\DataSource\MySql;

use Kernolab\Exception\UnknownOperandException;
use Kernolab\Model\DataSource\Criteria;
use Kernolab\Model\DataSource\MySql\QueryGenerator;
use PHPUnit\Framework\TestCase;

class QueryGeneratorTest extends TestCase
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
     * @runInSeparateProcess
     *
     * @param $target
     * @param $input
     * @param $expected
     *
     * @throws \Kernolab\Exception\UnknownOperandException
     */
    public function testParseCriteria($target, $input, $expected): void
    {
        $result = $this->criteria->parseRetrieval($target, $input);
        
        $this->assertEquals($expected, $result);
    }
    
    /**
     * @runInSeparateProcess
     * @throws \Kernolab\Exception\UnknownOperandException
     */
    public function testParseCriteriaException(): void
    {
        $input = [new Criteria('id', 'ez', 3),];
        
        $this->expectException(UnknownOperandException::class);
        $this->criteria->parseRetrieval('mock_table', $input);
    }
    
    public function parseCriteriaProvider(): array
    {
        return [
            'zero criteria'     => [
                'mock_table',
                [],
                [
                    'query' => 'SELECT * FROM `mock_table`',
                    'args'  => [],
                ]
            ],
            'one criteria'      => [
                'mock_table',
                [
                    new Criteria('id', 'eq', 3),
                ],
                [
                    'query' => 'SELECT * FROM `mock_table` WHERE `id` = ?',
                    'args'  => ['id' => 3],
                ],
            ],
            'multiple criteria' => [
                'mock_table',
                [
                    new Criteria('id', 'eq', 3),
                    new Criteria('gender', 'eq', 'male')
                ],
                [
                    'query' => 'SELECT * FROM `mock_table` WHERE `id` = ? AND `sex` = ?',
                    'args'  => ['id' => 3, 'gender' => 'male'],
                ],
            ]
        ];
    }
    
    /**
     * @dataProvider parseInsertionProvider
     * @runInSeparateProcess
     *
     * @param $target
     * @param $columns
     * @param $skipEntityId
     * @param $expected
     */
    public function testParseInsertion($target, $columns, $skipEntityId, $expected): void
    {
        $result = $this->criteria->parseInsertion($target, $columns, $skipEntityId);
        
        $this->assertEquals($expected, $result);
    }
    
    public function parseInsertionProvider(): array
    {
        return [
            [
                'mock_table',
                [
                    'col1',
                    'col2'
                ],
                false,
                'INSERT INTO `mock_table` (`col1`, `col2`) VALUES (?, ?)' .
                ' ON DUPLICATE KEY UPDATE `col1` = VALUES(`col1`), `col2` = VALUES(`col2`)'
            ],
            [
                'mock_table',
                [
                    'col1',
                    'col2'
                ],
                true,
                'INSERT INTO `mock_table` (`col2`) VALUES (?)' .
                ' ON DUPLICATE KEY UPDATE `col2` = VALUES(`col2`)'
            ]
        ];
    }
}
