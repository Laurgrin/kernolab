<?php

namespace Kernolab\Model\DataSource\MySql;

use Kernolab\Exception\MySqlConnectionException;
use Kernolab\Exception\MySqlPreparedStatementException;
use Kernolab\Model\DataSource\MySql\Query\DataSourceInterface;
use Kernolab\Model\EntityInterface;

class DataSource implements DataSourceInterface
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
     * @var \mysqli_stmt
     */
    protected $statement;
    
    /**
     * DataSource constructor.
     *
     * @throws \Kernolab\Exception\MySqlConnectionException
     */
    public function __construct()
    {
        $credentials = json_decode(file_get_contents(ENV_PATH), true)["db"];
    
        $this->setConnection($credentials);
    }
    
    /**
     * Connects to the databse
     *
     * @param array $credentials
     *
     * @throws \Kernolab\Exception\MySqlConnectionException
     */
    protected function setConnection(array $credentials)
    {
        $connection = mysqli_connect(
            $credentials["host"],
            $credentials["user"],
            $credentials["password"],
            $credentials["database"]
        );
    
        if (!$connection) {
            $errorNumber = mysqli_connect_errno();
            $errorMessage = mysqli_connect_error();
            throw new MySqlConnectionException(
                "Unable to connect to the database. Error $errorNumber: $errorMessage"
            );
        }
        $this->connection = $connection;
    
        $this->connection = $connection;
    }
    
    /**
     * Prepares the given statement and returns its handle.
     *
     * @param $query
     *
     * @return \Kernolab\Model\DataSource\MySql\DataSource
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     */
    protected function prepare($query)
    {
        $this->statement = $this->connection->prepare($query);
        
        if (!$this->statement) {
            $this->throwException($this->connection, "An error occurred while preparing the statement: ");
        }
        
        return $this;
    }
    
    /**
     * Binds params to a statement. Amount of types must match params.
     *
     * @param string   $types
     * @param string[] $params
     *
     * @return \Kernolab\Model\DataSource\MySql\DataSource
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     */
    protected function bindParams(string $types, $params)
    {
        if (!$this->statement->bind_param($types, ...$params)) {
            $this->throwException($this->statement, "An error occurred while binding params to the statement:");
        }
        
        return $this;
    }
    
    /**
     * Executes a prepared statement.
     *
     * @return \Kernolab\Model\DataSource\MySql\DataSource
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     */
    public function executeCommand()
    {
        if (!$this->statement->execute()) {
            $this->throwException($this->statement, "An error occurred while executing the statement: ");
        }
        
        return $this;
    }
    
    /**
     * Throws an exception with customized context message, listing all errors that occurred.
     *
     * @param \mysqli_stmt $statement
     * @param string       $contextMessage
     *
     * @return void
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     */
    protected function throwException($statement, string $contextMessage)
    {
        if ($statement->error_list) {
            foreach ($statement->error_list as $error) {
                $contextMessage .= "\n{$error["error"]}";
            }
            
            throw new MySqlPreparedStatementException($contextMessage);
        }
    }
    
    /**
     * Fetches the results of the query after all the prerequisite actions have been done. Will return either rows
     * affected or the result set. Basically, it will return the results of the executed statement, regardless of
     * what statement it is.
     *
     * @param string $command
     * @param string $types
     * @param array  $args
     *
     * @return int|array
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     */
    protected function execute(string $command, string $types = "", array $args = [])
    {
        $this->prepare($command);
        
        /* Skip binding params if there are no params. */
        if (count($args)) {
            $this->bindParams($types, $args);
        }
        $this->executeCommand();
        
        /* If metadata is false AND there are no errors, it means it wasn't a select statement,
        and we can return affected rows instead. If there is an error, something went terribly wrong.
        If we have metadata, it means it was a SELECT statement (or SHOW, EXPLAIN, etc.), then we return that. */
        if (!$this->statement->result_metadata() && empty($this->statement->error_list)) {
            return $this->statement->affected_rows;
        } elseif (!$this->statement->result_metadata() && empty($this->statement->error_list)) {
            $this->throwException($this->statement, "Error while trying to check metadata: ");
        }
        
        return $this->statement->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    
    /**
     * Gets data from data source.
     *
     * @param \Kernolab\Model\DataSource\Criteria[] $criteria
     *
     * @return mixed
     */
    public function get(array $criteria = [])
    {
        // TODO: Implement get() method.
    }
    
    /**
     * Saves entities to DataSource
     *
     * @param EntityInterface[] $entities
     *
     * @return mixed
     */
    public function set(array $entities)
    {
        // TODO: Implement set() method.
    }
}