<?php

namespace Model\DataSource\MySql;

use Model\DataSource\ConnectionInterface;

require_once(__DIR__ . "/../../../Autoload/Autoload.php");

class MySqlDataSource implements MySqlDataSourceInterface
{
    /**
     * @var \Model\DataSource\ConnectionInterface
     */
    protected $connection;
    
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }
    
    /**
     * Execute a query.
     *
     * @return mixed
     */
    public function executeQuery()
    {
        // TODO: Implement executeQuery() method.
    }
}