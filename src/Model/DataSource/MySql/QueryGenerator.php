<?php

namespace Kernolab\Model\DataSource\MySql;

use Kernolab\Exception\UnknownOperandException;
use Kernolab\Model\DataSource\Criteria;
use Kernolab\Model\DataSource\QueryGeneratorInterface;
use Kernolab\Model\Entity\EntityInterface;
use src\Model\Entity\EntityParserInterface;

class QueryGenerator implements QueryGeneratorInterface
{
    /**
     * Receives an associative array to create criteria. Basically, should parse it into a form the data source can
     * understand, such as a query for MySql.
     *
     * @param string                                $target
     * @param \Kernolab\Model\DataSource\Criteria[] $criteria
     *
     * @return array
     * @throws \Kernolab\Exception\UnknownOperandException
     */
    public function parseRetrieval(string $target, array $criteria = []): array
    {
        $query = "SELECT * FROM `$target`";
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
     * Takes an array of strings to parse into a insertion command for the data source. For MySql, this should create
     * an INSERT INTO statement using the columns provided.
     *
     * @param string $target
     * @param array  $columns
     * @param bool   $skipEntityId
     *
     * @return mixed
     */
    public function parseInsertion(string $target, array $columns, bool $skipEntityId = false)
    {
        /* If entity id is 0, we skip the first iteration as to not write the entity id into the query. */
        $iterator = 0;
        if ($skipEntityId) {
            $iterator = 1;
        }
    
        $columnsString = "";
        $valuesString  = "";
        $updateString  = "";
        for ($i = $iterator; $i < count($columns); $i++) {
            $columnsString .= "`{$columns[$i]}`, ";
            $valuesString  .= "?, ";
            $updateString  .= "`{$columns[$i]}` = VALUES(`{$columns[$i]}`), ";
        }
    
        $columnsQuery = sprintf("(%s)", rtrim($columnsString, ", "));
        $valuesQuery  = sprintf("VALUES (%s)", rtrim($valuesString, ", "));
        $updateQuery  = sprintf("ON DUPLICATE KEY UPDATE %s", rtrim($updateString, ", "));
    
        $command = sprintf("INSERT INTO `$target` %s %s %s", $columnsQuery, $valuesQuery, $updateQuery);
    
        return $command;
    }
}