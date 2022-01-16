<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
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
            "{$leftKey->getTable()}_{$leftKey->getPrimaryIndex()}";
        $this->rightColumn =
            "{$rightKey->getTable()}_{$rightKey->getPrimaryIndex()}";
        $this->leftKey = $leftKey;
        $this->rightKey = $rightKey;
    }


    public function getTableName(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $this->tableName;

        return $result;
    }

    public function getLeftForeign(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $this->leftColumn;

        return $result;
    }

    public function getRightForeign(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
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
