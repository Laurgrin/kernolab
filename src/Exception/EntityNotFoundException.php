<?php declare(strict_types = 1);

namespace Kernolab\Exception;

class EntityNotFoundException extends AbstractException
{
    /**
     * @var string
     */
    protected $entity;
    
    /**
     * @var int
     */
    protected $entityId;
    
    public function __construct(
        string $entity,
        int $entityId,
        int $code = 1,
        \Exception $previous = null
    ) {
        $entityName = explode('\\', $entity);
        $message = sprintf('Entity %s with the ID %s not found', end($entityName), $entityId);
        
        parent::__construct($message, $code, $previous);
        $this->entity   = $entity;
        $this->entityId = $entityId;
    }
    
    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }
    
    /**
     * @return int
     */
    public function getEntityId(): int
    {
        return $this->entityId;
    }
}