<?php

namespace Model\DataSource\MySql;

require_once(__DIR__ . "/../../../Autoload/Autoload.php");

use Exception\MySqlConnectionException;
use Model\DataSource\ConnectionInterface;

class MySqlConnection implements ConnectionInterface
{
    /** @var int */
    protected $user;
    
    /** @var string */
    protected $password;
    
    /** @var string */
    protected $database;
    
    /** @var string */
    protected $host;
    
    /** @var \mysqli */
    protected $connection;
    
    /**
     * MySqlConnection constructor.
     *
     * @param $host
     * @param $user
     * @param $password
     * @param $database
     */
    public function __construct()
    {
        //TODO: Remove hardcoding of MySql DB
        $credentials = json_decode($envContents, true)["db"];
        
        $this->host     = $credentials["host"];
        $this->user     = $credentials["user"];
        $this->password = $credentials["password"];
        $this->database = $credentials["database"];
    }
    
    /**
     * Connects to or otherwise accesses the data from the data source and returns it.
     *
     * @return mixed
     * @throws \Exception\MySqlConnectionException
     */
    public function connect()
    {
        $connection = mysqli_connect($this->host, $this->user, $this->password, $this->database);
        
        if (!$connection) {
            $errorNumber = mysqli_connect_errno();
            $errorMessage = mysqli_connect_error();
            throw new MySqlConnectionException(
                "Unable to connect to the database. Error $errorNumber: $errorMessage"
            );
        }
        $this->connection = $connection;
        
        return $connection;
    }
    
    /**
     * Manually close the connection to the database..
     *
     * @param \mysqli $connection
     */
    public function disconnect(\mysqli $connection)
    {
        mysqli_close($connection);
    }
}