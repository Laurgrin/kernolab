<?php

namespace Kernolab\Model\Entity;

interface EntityParserInterface
{
    /**
     * Returns an associate array of the entity properties using reflection.
     *
     * @param EntityInterface[] $entities
     *
     * @return array
     */
    public function getEntityProperties(array $entities): array;
    
    /**
     * Returns the target source for entity, for MySql that would be the table.
     *
     * @param \Kernolab\Model\Entity\EntityInterface $entity
     *
     * @return string
     */
    public function getEntityTarget(EntityInterface $entity): string;
}