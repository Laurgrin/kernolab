<?php declare(strict_types = 1);

namespace Kernolab\Exception;

class UndefinedRouteException extends AbstractException
{
    /**
     * @var string
     */
    protected $route;
    
    /**
     * UndefinedRouteException constructor.
     *
     * @param string          $route
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct(
        string $route,
        int $code = 1,
        \Exception $previous = null
    ) {
        $message = sprintf('Route %s not found', $route);
        
        parent::__construct($message, $code, $previous);
        $this->route = $route;
    }
    
    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }
}