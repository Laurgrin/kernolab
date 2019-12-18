<?php

namespace Model\DataSource\MySql\Query;

interface QueryInterface
{
    /**
     * Returns the result of the query.
     *
     * @return array|bool
     */
    public function fetch();
}