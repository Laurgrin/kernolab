<?php

namespace Kernolab\Model\DataSource\MySql;

use Kernolab\Exception\UnknownOperandException;
use Kernolab\Model\DataSource\CriteriaInterface;

class Criteria implements CriteriaInterface
{
    /**
     * @var string
     */
    protected $table;
    
    /**
     * Criteria constructor.
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
     * @param array $criteria
     *
     * @return array
     * @throws \Kernolab\Exception\UnknownOperandException
     */
    public function parseCriteria(array $criteria): array
    {
        $query = "SELECT * FROM `$this->table`";
        $params = [];
        
        if (!empty($criteria)) {
            $query .= " WHERE ";
            foreach ($criteria as $criterion) {
                $query .= "`{$criterion["field"]}` ";
                
                /* Can add new operands as needed. */
                switch ($criterion["operand"]) {
                    case self::OPERAND_EQUALS:
                        $query .= "= ?";
                        break;
                    default:
                        throw new UnknownOperandException(
                            "Operand {$criterion["operand"]} is not defined in " . __METHOD__
                        );
                        break;
                }
                $params[$criterion["field"]] = $criterion["value"];
                $query .= " AND ";
            }
        }
        $query = rtrim($query, " AND");
        
        return ["query" => $query, "args" => $params];
    }
    
    /**
     * @param string $table
     *
     * @return Criteria
     */
    public function setTable(string $table): Criteria
    {
        $this->table = $table;
        
        return $this;
    }
}