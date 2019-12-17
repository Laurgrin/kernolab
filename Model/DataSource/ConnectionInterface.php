<?php

namespace Model\DataSource;

interface ConnectionInterface
{
    /**
     * Connects to or otherwise accesses the data from the data source
     *
     * @return mixed
     */
    public function connect();
}