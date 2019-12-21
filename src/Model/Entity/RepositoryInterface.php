<?php

namespace Kernolab\Model\DataSource\MySql;

use Kernolab\Model\EntityInterface;

interface RepositoryInterface
{
    /**
     * Saves the entity to the database.
     *
     * @param \Kernolab\Model\EntityInterface $entity
     *
     * @return mixed
     */
    public function save(EntityInterface $entity);
    
    /**
     * Gets all rows from the database.
     *
     * @return EntityInterface
     */
    public function get();
}