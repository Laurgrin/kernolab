<?php

namespace Kernolab\Model\DataSource\MySql;

use Kernolab\Model\DataSource\MySql\Query\DataSourceInterface;
use Kernolab\Model\EntityInterface;

class TransactionRepository implements RepositoryInterface
{
    /**
     * @var \Kernolab\Model\DataSource\MySql\Query\DataSourceInterface
     */
    protected $query;
    
    public function __construct(DataSourceInterface $query)
    {
        $this->query = $query;
    }
    
    /**
     * Saves the entity to the database.
     *
     * @param \Kernolab\Model\EntityInterface $entity
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
     * @return void
     */
    public function get()
    {
        // TODO: Implement get() method.
    }
}