<?php

namespace Kernolab\Model\Entity;

class EntityParser implements EntityParserInterface
{
    /**
     * Returns an associate array of the entity properties using reflection.
     *
     * @param \Kernolab\Model\Entity\EntityInterface $entity
     *
     * @return array
     * @throws \ReflectionException
     */
    public function getEntityProperties(EntityInterface $entity): array
    {
        $entityProperties = [];
        $reflection       = new \ReflectionClass($entity);
        $properties       = $reflection->getProperties();
        
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $name = $this->toSnakeCase($property->getName());
            
            /* We should not update those manually, the db schema itself should take care of it. */
            if ($name === "created_at" || $name === "updated_at") {
                continue;
            }
            
            /* Skip this property if it is not set. */
            $value = $property->getValue($entity);
            if (empty($value)) {
                continue;
            }
            
            $entityProperties[$name] = $value;
        }
        
        return $entityProperties;
    }
    
    /**
     * Converts a string from camelCase to snake_case
     *
     * @param string $name
     *
     * @return string
     */
    public function toSnakeCase(string $name): string
    {
        $name[0]  = strtolower($name[0]);
        $function = function($char) {
            return "_" . strtolower($char[1]);
        };
        
        return preg_replace_callback(
            '/([A-Z])/',
            $function,
            $name
        );
    }
    
    /**
     * Returns the target source for entity, for MySql that would be the table.
     *
     * @param \Kernolab\Model\Entity\EntityInterface $entity
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getEntityTarget(EntityInterface $entity): string
    {
        $reflection = new \ReflectionClass($entity);
        
        return strtolower($reflection->getShortName());
    }
}