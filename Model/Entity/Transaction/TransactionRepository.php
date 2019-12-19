<?php

namespace Model\DataSource\MySql;

use Model\DataSource\MySql\Query\DataSourceInterface;
use Model\EntityInterface;

class TransactionRepository implements RepositoryInterface
{
    /**
     * @var \Model\DataSource\MySql\Query\DataSourceInterface
     */
    protected $query;
    
    public function __construct(DataSourceInterface $query)
    {
        $this->query = $query;
    }
    
    /**
     * Saves the entity to the database.
     *
     * @param \Model\EntityInterface $entity
     *
     * @return mixed
     */
    public function save(EntityInterface $entity)
    {
        // TODO: Implement save() method.
    }
    
    /**
     * Gets all rows from the database.
     *
     * @return EntityInterface
     */
    public function get()
    {
        // TODO: Implement get() method.
    }
}