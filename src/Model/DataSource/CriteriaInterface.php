<?php

namespace Kernolab\Model\DataSource;

interface CriteriaInterface
{
    const OPERAND_EQUALS = "eq";
    
    /**
     * Should take an assoc array of generic criteria as an argument for the setter.
     * It should look something like [["field" => "id", "operand" => "eq", "value" => 1], [...], ...].
     * All elements of the array should be parsed as AND. Any specific criteria systems, for example, MySql,
     * should extend this and interpret it however it is needed.
     *
     * @param array $criteria
     *
     * @return mixed
     */
    public function parseCriteria(array $criteria);
}