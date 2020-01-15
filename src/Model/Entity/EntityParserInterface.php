<?php  declare(strict_types = 1);

namespace Kernolab\Model\Entity;

/**
 * Interface EntityParserInterface
 * @package Kernolab\Model\Entity
 * @codeCoverageIgnore
 */
interface EntityParserInterface
{
    /**
     * Returns an associate array of the entity properties using reflection.
     *
     * @param \Kernolab\Model\Entity\EntityInterface $entity
     *
     * @return array
     */
    public function getEntityProperties(EntityInterface $entity): array;
    
    /**
     * Returns the target source for entity, for MySql that would be the table.
     *
     * @param \Kernolab\Model\Entity\EntityInterface $entity
     *
     * @return string
     */
    public function getEntityTarget(EntityInterface $entity): string;
}