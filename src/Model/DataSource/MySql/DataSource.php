<?php

namespace Kernolab\Model\DataSource\MySql;

use Kernolab\Exception\MySqlConnectionException;
use Kernolab\Exception\MySqlPreparedStatementException;
use Kernolab\Model\DataSource\QueryGeneratorInterface;
use Kernolab\Model\DataSource\DataSourceInterface;
use Kernolab\Model\Entity\EntityInterface;
use Kernolab\Model\Entity\EntityParserInterface;

class DataSource implements DataSourceInterface
{
    /**
     * @var \mysqli
     */
    protected $connection;
    
    /**
     * @var QueryGeneratorInterface
     */
    protected $queryGenerator;
    
    /**
     * @var \Kernolab\Model\Entity\EntityParserInterface
     */
    protected $entityParser;
    
    /**
     * DataSource constructor.
     *
     * @param QueryGeneratorInterface                      $queryGenerator
     *
     * @param \Kernolab\Model\Entity\EntityParserInterface $entityParser
     *
     * @throws \Kernolab\Exception\MySqlConnectionException
     */
    public function __construct(QueryGeneratorInterface $queryGenerator, EntityParserInterface $entityParser)
    {
        $credentials          = json_decode(file_get_contents(ENV_PATH), true)["db"];
        $this->queryGenerator = $queryGenerator;
        $this->entityParser   = $entityParser;
        $this->setConnection($credentials);
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
        if (!$statement->bind_param(str_repeat("s", count($params)), ...array_values($params))) {
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
        if (!$statement->result_metadata() && empty($statement->error_list)) {
            return $statement->affected_rows;
        } elseif (!$statement->result_metadata() && empty($statement->error_list)) {
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
     * @param string                                $table
     *
     * @return array
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     */
    public function get(array $criteria = [], string $table = ""): array
    {
        $parsedCriteria = $this->queryGenerator->parseRetrieval($table, $criteria);
        $command        = $parsedCriteria["query"];
        
        $results = $this->getResult(
            $this->executeStatement($this->bindParams($this->prepare($command), $parsedCriteria["args"]))
        );
        
        return $results;
    }
    
    /**
     * Saves an entity to DataSource. If a new entity was created, it will be returned with its ID set.
     *
     * @param \Kernolab\Model\Entity\EntityInterface $entity
     *
     * @return \Kernolab\Model\Entity\EntityInterface
     * @throws \Kernolab\Exception\MySqlPreparedStatementException
     */
    public function set(EntityInterface $entity): EntityInterface
    {
        $savedEntities = [];
        
        /* We only want to prepare the statement once, and then bind and execute it with different values. */
        $entityProperties  = $this->entityParser->getEntityProperties($entity);
        $columns       = array_keys($entityProperties);
        $query        =
            $this->queryGenerator->parseInsertion($this->entityParser->getEntityTarget($entity), $columns);
        $statement    = $this->prepare($query);
        if ($entity->getEntityId() == 0) {
            unset($entityProperties["entity_id"]);
        }
    
        $statement = $this->bindParams($statement, array_values($entityProperties));
        $this->executeStatement($statement);
        $entity->setEntityId($this->connection->insert_id);
        $savedEntities[] = $entity;
       
        
        return $entity;
    }
}