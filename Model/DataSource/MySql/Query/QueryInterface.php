<?php

namespace Model\DataSource\MySql\Query;

interface QueryInterface
{
    /**
     * Returns the result of the query executed with the provided arguments.
     *
     * @param string $query
     * @param string $types
     * @param array  $args
     *
     * @return array|bool
     */
    public function fetch(string $query, string $types = "", array $args = []);
}