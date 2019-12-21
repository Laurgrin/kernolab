<?php

namespace Kernolab\Model\DataSource\MySql;

use Kernolab\Model\Entity\EntityInterface;

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
     * @return bool
     */
    public function set(array $entities): bool;
}