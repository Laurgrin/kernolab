<?php

namespace Kernolab\Controller;

/**
 * Interface JsonResponseInterface
 * @package Kernolab\Controller
 * @codeCoverageIgnore
 */
interface JsonResponseInterface
{
    /**
     * Adds a data field to the response
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return \Kernolab\Controller\JsonResponseInterface
     */
    public function addField(string $key, $value): JsonResponseInterface;
    
    /**
     * Adds an error to the response.
     *
     * @param int    $code
     * @param string $message
     *
     * @return \Kernolab\Controller\JsonResponseInterface
     */
    public function addError(int $code, string $message): JsonResponseInterface;
    
    /**
     * Returns a JSON encoded response.
     *
     * @return string
     */
    public function getResponse(): string;
}