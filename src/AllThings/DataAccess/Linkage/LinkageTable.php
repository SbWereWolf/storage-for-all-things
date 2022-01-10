<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */

namespace AllThings\DataAccess\Linkage;

class LinkageTable implements ILinkageTable
{
    private string $tableName;
    private string $leftColumn;
    private string $rightColumn;

    public function __construct(
        string $tableName,
        string $leftColumn,
        string $rightColumn
    ) {
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
