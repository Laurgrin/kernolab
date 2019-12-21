<?php

namespace Kernolab\Model\DataSource\MySql\Query;

use Kernolab\Model\EntityInterface;

interface DataSourceInterface
{
    /**
     * Gets data from data source.
     *
     * @param \Kernolab\Model\DataSource\Criteria[] $criteria
     *
     * @return mixed
     */
    public function get(array $criteria = []);
    
    /**
     * Saves entities to DataSource
     *
     * @param EntityInterface[] $entities
     *
     * @return mixed
     */
    public function set(array $entities);
}