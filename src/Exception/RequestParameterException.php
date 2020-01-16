<?php declare(strict_types = 1);

namespace Kernolab\Exception;

class RequestParameterException extends AbstractException
{
    /**
     * @var array
     */
    protected $missingKeys;
    
    public function __construct(
        string $message,
        array $missingKeys,
        int $code = 1,
        \Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->missingKeys = $missingKeys;
    }
    
    /**
     * Returns the missing request keys.
     *
     * @return array
     */
    public function getMissingKeys(): array
    {
        return $this->missingKeys;
    }
}