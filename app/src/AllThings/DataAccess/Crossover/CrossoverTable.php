<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:39
 */

namespace AllThings\DataAccess\Crossover;


class CrossoverTable implements ICrossoverTable
{
    private $tableName = '';
    private $leftColumn = '';
    private $rightColumn = '';

    public function __construct(string $tableName, string $leftColumn, string $rightColumn)
    {
        $this->tableName = $tableName;
        $this->leftColumn = $leftColumn;
        $this->rightColumn = $rightColumn;
    }


    public function getTableName(): string
    {
        $result = $this->tableName;

        return $result;
    }

    public function getLeftColumn(): string
    {
        $result = $this->leftColumn;

        return $result;
    }

    public function getRightColumn(): string
    {
        $result = $this->rightColumn;

        return $result;
    }

}
