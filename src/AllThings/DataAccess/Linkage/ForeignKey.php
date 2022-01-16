<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Linkage;

class ForeignKey implements IForeignKey
{

    private string $table;
    private string $primary;
    private string $matching;

    /**
     * @param string $table
     * @param string $column
     * @param string $index
     */
    public function __construct(
        string $table,
        string $column,
        string $index
    ) {
        $this->primary = $column;
        $this->matching = $index;
        $this->table = $table;
    }

    public function getTable(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $this->table;

        return $result;
    }

    public function getPrimaryIndex(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $this->primary;

        return $result;
    }

    public function getMatchColumn(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $this->matching;

        return $result;
    }
}
