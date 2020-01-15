<?php  declare(strict_types = 1);

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
     * Saves entities to DataSource and returns it. If a new one is created, returns the entity with it's ID.
     *
     * @param EntityInterface $entity
     *
     * @return EntityInterface
     */
    public function set(EntityInterface $entity): EntityInterface;
}