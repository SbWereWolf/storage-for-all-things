<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 02.06.18 20:30
 */

namespace AllThings\DataObject;


class CrossoverTable implements ICrossoverTable
{
    private $tableName = '';
    private $leftColumn = '';
    private $rightColumn = '';

    public function __construct(\string $tableName, \string $leftColumn, \string $rightColumn)
    {
        $this->tableName = $tableName;
        $this->leftColumn = $leftColumn;
        $this->rightColumn = $rightColumn;
    }


    function getTableName(): \string
    {
        $result = $this->tableName;

        return $result;
    }

    function getLeftColumn(): \string
    {
        $result = $this->leftColumn;

        return $result;
    }

    function getRightColumn(): \string
    {
        $result = $this->rightColumn;

        return $result;
    }

}
