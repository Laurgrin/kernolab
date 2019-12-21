<?php

namespace Kernolab\Model\DataSource\MySql;

use Kernolab\Exception\UnknownOperandException;
use Kernolab\Model\DataSource\Criteria;
use Kernolab\Model\DataSource\CriteriaParserInterface;

class CriteriaParser implements CriteriaParserInterface
{
    /**
     * @var string
     */
    protected $table;
    
    /**
     * CriteriaParser constructor.
     *
     * @param string $table
     */
    public function __construct(string $table)
    {
        $this->setTable($table);
    }
    
    /**
     * Receives an associative array to create criteria. Basically, should parse it into a form the data source can
     * understand, such as a query for MySql.
     *
     * @param \Kernolab\Model\DataSource\Criteria[] $criteria
     *
     * @return array
     * @throws \Kernolab\Exception\UnknownOperandException
     */
    public function parseCriteria(array $criteria = []): array
    {
        $query = "SELECT * FROM `$this->table`";
        $params = [];
        
        if (!empty($criteria)) {
            $query .= " WHERE ";
            foreach ($criteria as $criterion) {
                $query .= "`{$criterion->getField()}` ";
                
                /* Can add new operands as needed. */
                switch ($criterion->getOperand()) {
                    case Criteria::OPERAND_EQUALS:
                        $query .= "= ?";
                        break;
                    default:
                        throw new UnknownOperandException(
                            "Operand {$criterion->getOperand()} is not defined.");
                        break;
                }
                $params[$criterion->getField()] = $criterion->getValue();
                $query .= " AND ";
            }
        }
        $query = rtrim($query, " AND");
        
        return ["query" => $query, "args" => $params];
    }
    
    /**
     * @param string $table
     *
     * @return CriteriaParser
     */
    public function setTable(string $table): CriteriaParser
    {
        $this->table = $table;
        
        return $this;
    }
}