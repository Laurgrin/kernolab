<?php

namespace Model\DataSource\MySql;

interface MySqlDataSourceInterface
{
    /**
     * Execute a query.
     *
     * @return mixed
     */
    public function executeQuery();
}