<?php

namespace Kernolab\Model\Entity;

/**
 * Interface EntityInterface
 * @package Kernolab\Model\Entity
 * @codeCoverageIgnore
 */
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
    
    /**
     * Sets the entity ID.
     *
     * @param int $id
     *
     * @return \Kernolab\Model\Entity\EntityInterface
     */
    public function setEntityId(int $id): EntityInterface;
}