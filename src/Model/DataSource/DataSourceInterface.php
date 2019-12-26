<?php

namespace Kernolab\Model\DataSource;

use Kernolab\Model\Entity\EntityInterface;

/**
 * Interface DataSourceInterface
 * @package Kernolab\Model\DataSource
 * @codeCoverageIgnore
 */
interface DataSourceInterface
{
    /**
     * Gets data from data source.
     *
     * @param \Kernolab\Model\DataSource\Criteria[] $criteria
     *
     * @return array
     */
    public function get(array $criteria): array;
    
    /**
     * Saves entities to DataSource
     *
     * @param EntityInterface[] $entities
     *
     * @return mixed
     */
    public function set(array $entities);
}