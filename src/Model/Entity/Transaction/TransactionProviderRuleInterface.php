<?php

namespace Kernolab\Model\Entity\Transaction;

interface TransactionProviderRuleInterface
{
    /**
     * Apply a transaction provider rule to transaction assoc. array.
     *
     * @param array $params
     *
     * @return array
     */
    public function applyProviderRules(array $params): array;
}