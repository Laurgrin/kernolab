<?php declare(strict_types = 1);

namespace Kernolab\Model\DataSource\MySql;

use Kernolab\Exception\MySqlConnectionException;
use Kernolab\Exception\MySqlPreparedStatementException;
use Kernolab\Model\DataSource\QueryGeneratorInterface;
use Kernolab\Model\DataSource\DataSourceInterface;
use Kernolab\Model\Entity\EntityInterface;
use Kernolab\Model\Entity\EntityParserInterface;
use mysqli;
use mysqli_stmt;

class DataSource implements DataSourceInterface
{
    /**
     * @var mysqli
     */
    protected $connection;
    
    /**
     * @var QueryGeneratorInterface
     */
    protected $queryGenerator;
    
    /**
     * @var EntityParserInterface
     */
    protected $entityParser;
    
    /**
     * DataSource constructor.
     *
     * @param QueryGeneratorInterface $queryGenerator
     *
     * @param EntityParserInterface   $entityParser
     *
     */
    public function __construct(QueryGeneratorInterface $queryGenerator, EntityParserInterface $entityParser)
    {
        $this->queryGenerator = $queryGenerator;
        $this->entityParser   = $entityParser;
    }
    
    public function __destruct()
    {
        if ($this->connection) {
            $this->connection->close();
        }
    }
    
    /**
     * Returns database credentials.
     *
     * @return array
     */
    protected function getConnectionCredentials(): array
    {
        return json_decode(
                   file_get_contents(ENV_PATH),
                   true,
                   512,
                   JSON_THROW_ON_ERROR
               )['db'];
    }
    
    /**
     * Gets the connection handle to the database. Throws an error otherwise.
     *
     * @param array $credentials
     *
     * @return \mysqli
     * @throws MySqlConnectionException
     */
    protected function getConnection(): mysqli
    {
        if (!$this->connection) {
            $credentials = $this->getConnectionCredentials();
    
            $connection = mysqli_connect(
                $credentials['host'],
                $credentials['user'],
                $credentials['password'],
                $credentials['database']
            );
    
            if (!$connection) {
                $errorNumber  = mysqli_connect_errno();
                $errorMessage = mysqli_connect_error();
                throw new MySqlConnectionException(
                    sprintf('Unable to connect to the database. Error %s: %s', $errorNumber, $errorMessage)
                );
            }
            
            $this->connection = $connection;
        }
        
        return $this->connection;
    }
    
    /**
     * Prepares the given statement and returns its handle.
     *
     * @param $query
     *
     * @return mysqli_stmt
     * @throws MySqlPreparedStatementException
     * @throws MySqlConnectionException
     */
    protected function prepare($query): mysqli_stmt
    {
        $connection = $this->getConnection();
        $statement = $connection->prepare($query);
        
        if (!$statement) {
            $this->throwException($connection, 'An error occurred while preparing the statement: ');
        }
        
        return $statement;
    }
    
    /**
     * Binds params to a statement. Amount of types must match params.
     *
     * @param mysqli_stmt $statement
     * @param string[]    $params
     *
     * @return mysqli_stmt
     * @throws MySqlPreparedStatementException
     */
    protected function bindParams(mysqli_stmt $statement, array $params): mysqli_stmt
    {
        $values = array_values($params);
        
        if (!$statement->bind_param(str_repeat('s', count($params)), ...$values)) {
            $this->throwException($statement, 'An error occurred while binding params to the statement:');
        }
        
        return $statement;
    }
    
    /**
     * Executes a prepared statement.
     *
     * @param mysqli_stmt $statement
     *
     * @return mysqli_stmt
     * @throws MySqlPreparedStatementException
     */
    protected function executeStatement(mysqli_stmt $statement): mysqli_stmt
    {
        if (!$statement->execute()) {
            $this->throwException($statement, 'An error occurred while executing the statement: ');
        }
        
        return $statement;
    }
    
    /**
     * Throws an exception with customized context message, listing all errors that occurred.
     *
     * @param mysqli_stmt|mysqli $statement
     * @param string             $contextMessage
     *
     * @return void
     * @throws MySqlPreparedStatementException
     */
    protected function throwException($statement, string $contextMessage): void
    {
        if ($statement->error_list) {
            foreach ($statement->error_list as $error) {
                $contextMessage .= sprintf(PHP_EOL . '%s', $error['error']);
            }
            
            throw new MySqlPreparedStatementException($contextMessage);
        }
    }
    
    /**
     * Gets results from an executed statement.
     *
     * @param mysqli_stmt $statement
     * @param bool        $closeOnReturn Should statement be closed when results are returned. Defaults to true.
     *
     * @return mixed
     * @throws MySqlPreparedStatementException
     */
    protected function getResult(mysqli_stmt $statement, bool $closeOnReturn = true)
    {
        /* If metadata is false AND there are no errors, it means it wasn't a select statement,
        and we can return affected rows instead. If there is an error, something went terribly wrong.
        If we have metadata, it means it was a SELECT statement (or SHOW, EXPLAIN, etc.), then we return that. */
        if (empty($statement->error_list) && !$statement->result_metadata()) {
            return $statement->affected_rows;
        }
        
        if (!empty($statement->error_list)) {
            $this->throwException($statement, 'Error while trying to check metadata: ');
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
     * @throws MySqlPreparedStatementException
     * @throws MySqlConnectionException
     */
    public function get(array $criteria = [], string $table = ''): array
    {
        $parsedCriteria = $this->queryGenerator->parseRetrieval($table, $criteria);
        $command        = $parsedCriteria['query'];
        
        $results = $this->getResult(
            $this->executeStatement($this->bindParams($this->prepare($command), $parsedCriteria['args']))
        );
        
        return $results;
    }
    
    /**
     * Saves an entity to DataSource. If a new entity was created, it will be returned with its ID set.
     *
     * @param EntityInterface $entity
     *
     * @return EntityInterface
     * @throws MySqlPreparedStatementException
     * @throws MySqlConnectionException
     */
    public function set(EntityInterface $entity): EntityInterface
    {
        $connection = $this->getConnection();
        
        /* We only want to prepare the statement once, and then bind and execute it with different values. */
        $entityProperties = $this->entityParser->getEntityProperties($entity);
        $columns          = array_keys($entityProperties);
        $query            =
            $this->queryGenerator->parseInsertion($this->entityParser->getEntityTarget($entity), $columns);
        $statement        = $this->prepare($query);
        if ($entity->getEntityId() === 0) {
            unset($entityProperties['entity_id']);
        }
        
        $statement = $this->bindParams($statement, array_values($entityProperties));
        $this->executeStatement($statement);
        $entity->setEntityId($connection->insert_id);
        
        return $entity;
    }
}