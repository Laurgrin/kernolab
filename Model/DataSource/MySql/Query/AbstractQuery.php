<?php

namespace Model\DataSource\MySql\Query;

use Exception\BindParamMismatchException;
use Exception\QueryExecutionException;

require_once(__DIR__ . "/../../../../Autoload/Autoload.php");

abstract class AbstractQuery implements QueryInterface
{
    /**
     * @var \mysqli
     */
    protected $connection;
    
    /**
     * @var int
     */
    protected $affectedRows = 0;
    
    /**
     * AbstractQuery constructor.
     *
     * @param \mysqli $connection
     */
    public function __construct(\mysqli $connection)
    {
        $this->connection = $connection;
    }
    
    /**
     * Prepares the given statement and returns its handle.
     *
     * @param $statement
     *
     * @return \mysqli_stmt
     */
    protected function prepare($statement)
    {
        return $this->connection->prepare($statement);
    }
    
    /**
     * Binds params to a statement. Amount of types must match params.
     *
     * @param \mysqli_stmt $statement
     * @param string       $types
     * @param string[]     $params
     *
     * @return \mysqli_stmt
     * @throws \Exception\BindParamMismatchException
     */
    protected function bindParams(\mysqli_stmt $statement, string $types, string ...$params)
    {
        $paramTypeCount = strlen($types);
        $paramCount     = count($params);
        
        if ($paramTypeCount != $paramCount) {
            throw new BindParamMismatchException(
                "Declared param type count ($paramTypeCount) does not match the given param count ($paramCount)."
            );
        }
        
        $statement->bind_param($types, ...$params);
        
        return $statement;
    }
    
    /**
     * Executes a prepared statement.
     *
     * @param \mysqli_stmt $statement
     *
     * @return \mysqli_stmt
     * @throws \Exception\QueryExecutionException
     */
    protected function execute(\mysqli_stmt $statement)
    {
        if (!$statement->execute()) {
            throw new QueryExecutionException("Query execution failed, check the statement or provided params.");
        }
        
        return $statement;
    }
    
    /**
     * Fetches the results of the query.
     *
     * @return array|bool
     */
    public abstract function fetch();
}