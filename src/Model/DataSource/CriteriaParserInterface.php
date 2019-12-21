<?php

namespace Kernolab\Model\DataSource;

interface CriteriaParserInterface
{
    /**
     * Takes an array of Criteria to parse into something understandable for the data source.
     *
     * @param \Kernolab\Model\DataSource\Criteria[] $criteria
     *
     * @return mixed
     */
    public function parseCriteria(array $criteria = []);
}