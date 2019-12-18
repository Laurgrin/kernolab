<?php

namespace Model;

interface EntityInterface
{
    /**
     * Returns the entity's ID (primary key).
     *
     * @return string
     */
    public function getEntityId();
    
    /**
     * Returns the time when entity was created.
     *
     * @return string
     */
    public function getCreatedAt();
    
    /**
     * Returns the time when the entity was last updated.
     *
     * @return mixed
     */
    public function getUpdatedAt();
}