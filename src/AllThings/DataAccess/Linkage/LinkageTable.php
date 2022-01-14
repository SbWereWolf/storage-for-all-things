<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 6:19
 */

namespace AllThings\DataAccess\Linkage;

class LinkageTable implements ILinkageTable
{
    private string $tableName;
    private string $leftColumn;
    private string $rightColumn;
    private IForeignKey $leftKey;
    private IForeignKey $rightKey;

    public function __construct(
        string $tableName,
        IForeignKey $leftKey,
        IForeignKey $rightKey
    ) {
        $this->tableName = $tableName;
        $this->leftColumn =
            "{$leftKey->getTable()}_{$leftKey->getColumn()}";
        $this->rightColumn =
            "{$rightKey->getTable()}_{$rightKey->getColumn()}";
        $this->leftKey = $leftKey;
        $this->rightKey = $rightKey;
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

    /**
     * @return IForeignKey
     */
    public function getLeftKey(): IForeignKey
    {
        return $this->leftKey;
    }

    /**
     * @return IForeignKey
     */
    public function getRightKey(): IForeignKey
    {
        return $this->rightKey;
    }
}
