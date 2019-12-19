<?php

namespace Model\DataSource\MySql;

use Model\EntityInterface;

interface RepositoryInterface
{
    /**
     * Saves the entity to the database.
     *
     * @param \Model\EntityInterface $entity
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