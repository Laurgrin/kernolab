<?php

namespace Kernolab\Model\DataSource;

interface QueryGeneratorInterface
{
    /**
     * Takes an array of Criteria to parse into something understandable for the data source.
     *
     * @param string                                $target The target of retrieval, like a MySql table.
     * @param \Kernolab\Model\DataSource\Criteria[] $criteria
     *
     * @return mixed
     */
    public function parseRetrieval(string $target, array $criteria = []);
    
    /**
     * Takes an array of data to parse into a insertion command for the data source. For MySql, this should create
     * an INSERT INTO statement using the columns provided in the $params array.
     *
     * @param string $target The target of retrieval, like a MySql table.
     * @param array  $params
     *
     * @return mixed
     */
    public function parseInsertion(string $target, array $params);
}