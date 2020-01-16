<?php  declare(strict_types = 1);

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
     * @return int
     */
    public function getEntityId(): int;
    
    /**
     * Returns the time when entity was created.
     *
     * @return string
     */
    public function getCreatedAt(): string;
    
    /**
     * Returns the time when the entity was last updated.
     *
     * @return mixed
     */
    public function getUpdatedAt(): string;
    
    /**
     * Sets the entity ID.
     *
     * @param int $id
     *
     * @return \Kernolab\Model\Entity\EntityInterface
     */
    public function setEntityId(int $id): EntityInterface;
}