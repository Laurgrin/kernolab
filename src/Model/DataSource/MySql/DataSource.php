<?php

namespace Kernolab\Model\DataSource\MySql;

use Kernolab\Exception\MySqlConnectionException;
use Kernolab\Exception\MySqlPreparedStatementException;
use Kernolab\Model\DataSource\CriteriaParserInterface;
use Kernolab\Model\DataSource\DataSourceInterface;
use Kernolab\Model\Entity\EntityInterface;

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
     * @var CriteriaParserInterface
     */
    protected $criteriaParser;
    
    /**
     * @var string
     */
    protected $table;
    
    /**
     * DataSource constructor.
     *
     * @param CriteriaParserInterface $criteriaParser
     *
     * @param string                  $table
     *
     * @throws \Kernolab\Exception\MySqlConnectionException
     */
    public function __construct(CriteriaParserInterface $criteriaParser, string $table)
    {
        $credentials = json_decode(file_get_contents(ENV_PATH), true)["db"];
        $this->setConnection($credentials);
        $this->criteriaParser = $criteriaParser;
        $this->setTable($table);
    }
    
    /**
     * Connects to the database and sets the connection handle
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
            $errorNumber  = mysqli_connect_errno();
            $errorMessage = mysqli_connect_error();
            throw new MySqlConnectionException(
                "Unable to connect to the database. Error $errorNumber: $errorMessage"
            );
        }
        
        $this->connection = $connection;
    }
    
    /**
     * Prepares the given statement and returns its handle.
     *
     * @param $query
     *
     * @return \mysqli_stmt
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     */
    protected function prepare($query)
    {
        $statement = $this->connection->prepare($query);
        
        if (!$statement) {
            $this->throwException($this->connection, "An error occurred while preparing the statement: ");
        }
        
        return $statement;
    }
    
    /**
     * Binds params to a statement. Amount of types must match params.
     *
     * @param \mysqli_stmt $statement
     * @param string[]     $params
     *
     * @return \mysqli_stmt
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     */
    protected function bindParams(\mysqli_stmt $statement, array $params)
    {
        if (!$statement->bind_param(str_repeat("s", count($params)), ...$params)) {
            $this->throwException($statement, "An error occurred while binding params to the statement:");
        }
        
        return $statement;
    }
    
    /**
     * Executes a prepared statement.
     *
     * @param \mysqli_stmt $statement
     *
     * @return \mysqli_stmt
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     */
    protected function executeStatement(\mysqli_stmt $statement)
    {
        if (!$statement->execute()) {
            $this->throwException($statement, "An error occurred while executing the statement: ");
        }
        
        return $statement;
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
     * Gets results from an executed statement.
     *
     * @param \mysqli_stmt $statement
     * @param bool         $closeOnReturn Should statement be closed when results are returned. Defaults to true.
     *
     * @return mixed
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     */
    protected function getResult(\mysqli_stmt $statement, bool $closeOnReturn = true)
    {
        /* If metadata is false AND there are no errors, it means it wasn't a select statement,
        and we can return affected rows instead. If there is an error, something went terribly wrong.
        If we have metadata, it means it was a SELECT statement (or SHOW, EXPLAIN, etc.), then we return that. */
        if (!$statement->result_metadata() && empty($this->statement->error_list)) {
            return $statement->affected_rows;
        } elseif (!$statement->result_metadata() && empty($this->statement->error_list)) {
            $this->throwException($statement, "Error while trying to check metadata: ");
        }
        
        $result = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
        if ($closeOnReturn) {
            $statement->close();
        }
        
        return $result;
    }
    
    /**
     * Gets data from data source.
     *
     * @param \Kernolab\Model\DataSource\Criteria[] $criteria
     *
     * @return mixed
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     */
    public function get(array $criteria = [])
    {
        $parsedCriteria = $this->criteriaParser->parseCriteria($criteria);
        $command        = $parsedCriteria["query"];
        
        $result = $this->getResult(
            $this->executeStatement($this->bindParams($this->prepare($command), $parsedCriteria["args"]))
        );
        
        return $result;
    }
    
    /**
     * Saves entities to DataSource
     *
     * @param EntityInterface[] $entities
     *
     * @return bool
     */
    public function set(array $entities): bool
    {
        $dataArray    = $this->getFields($entities);
        $affectedRows = 0;
        
        /* Get the columns for query generation. All of them will be the same, so we grab it from the first element. */
        $columns      = array_keys($dataArray[0]);
        $skipEntityId = $dataArray[0]["entity_id"] == 0;
        $query        = $this->generateInsertQuery($columns, $skipEntityId);
        try {
            $statement = $this->prepare($query);
    
            foreach ($dataArray as $column => $value) {
                if ($skipEntityId) {
                    unset($value["entity_id"]);
                }
        
                $statement    = $this->bindParams($statement, array_values($value));
                $affectedRows += $this->getResult($this->executeStatement($statement));
            }
        } catch (MySqlPreparedStatementException $e) {
            echo $e->getMessage() . PHP_EOL;
            return false;
        }
        
        return true;
    }
    
    /**
     * Generates an insert query from an array of entities.
     *
     * @param array $columns
     * @param bool  $skipEntityId
     *
     * @return string
     */
    protected function generateInsertQuery(array $columns, bool $skipEntityId = false): string
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
        
        $command = sprintf("INSERT INTO `%s` %s %s %s", $this->table, $columnsQuery, $valuesQuery, $updateQuery);
        
        return $command;
    }
    
    /**
     * Returns an associate array of the entity properties using reflection.
     *
     * @param EntityInterface[] $entities
     *
     * @return array
     */
    protected function getFields(array $entities): array
    {
        $dataArray = [];
        
        foreach ($entities as $entity) {
            $entityProperties = [];
            try {
                $reflection = new \ReflectionClass($entity);
            } catch (\ReflectionException $e) {
                echo $e->getMessage() . PHP_EOL;
                continue;
            }
            $properties = $reflection->getProperties();
            
            foreach ($properties as $property) {
                $property->setAccessible(true);
                $name = $this->toSnakeCase($property->getName());
                
                /* We should not update those manually, the db schema itself should take care of it. */
                if ($name === "created_at" || $name === "updated_at") {
                    continue;
                }
                
                $value = $property->getValue($entity);
                
                $entityProperties[$name] = $value;
            }
            
            $dataArray[] = $entityProperties;
        }
        
        return $dataArray;
    }
    
    /**
     * Converts a string from camelCase to snake_case
     *
     * @param string $name
     *
     * @return string
     */
    protected function toSnakeCase(string $name): string
    {
        $name[0]  = strtolower($name[0]);
        $function = function($char) {
            return "_" . strtolower($char[1]);
        };
        
        return preg_replace_callback(
            '/([A-Z])/',
            $function,
            $name
        );
    }
    
    /**
     * @param string $table
     */
    public function setTable(string $table): void
    {
        $this->table = $table;
        $this->criteriaParser->setTable($table);
    }
}