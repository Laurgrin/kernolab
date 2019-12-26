<?php

namespace Kernolab\Model\DataSource;

/**
 * Class Criteria
 * @package Kernolab\Model\DataSource
 * @codeCoverageIgnore
 */
final class Criteria
{
    const OPERAND_EQUALS = "eq";
    
    /**
     * @var string
     */
    private $field;
    
    /**
     * @var string
     */
    private $operand;
    
    /**
     * @var string
     */
    private $value;
    
    /**
     * Criteria constructor.
     *
     * @param string $field The criteria to search for, eg. name.
     * @param string $operand The operand to use for evaluation, like eq (=)
     * @param string $value The value to evaluate for the field using the operand.
     */
    public function __construct($field, $operand, $value)
    {
        $this->field   = $field;
        $this->operand = $operand;
        $this->value   = $value;
    }
    
    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }
    
    /**
     * @param mixed $field
     */
    public function setField($field): void
    {
        $this->field = $field;
    }
    
    /**
     * @return mixed
     */
    public function getOperand()
    {
        return $this->operand;
    }
    
    /**
     * @param mixed $operand
     */
    public function setOperand($operand): void
    {
        $this->operand = $operand;
    }
    
    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }
}