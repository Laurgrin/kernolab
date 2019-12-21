<?php

namespace Kernolab\Model\DataSource\MySql\Query;

interface DataSourceInterface
{
    /**
     * Executes a command on a data source and returns the result.
     *
     * @param string $command
     * @param string $types
     * @param array  $args
     *
     * @return array|bool
     */
    public function executeStatement(string $command, string $types = "", array $args = []);
    
    public function get();
    
    public function set();
}