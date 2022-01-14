<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 6:19
 */

namespace AllThings\DataAccess\Linkage;

class ForeignKey implements IForeignKey
{

    private string $table;
    private string $column;
    private string $index;

    /**
     * @param string $table
     * @param string $column
     * @param string $index
     */
    public function __construct(string $table, string $column, string $index)
    {
        $this->column = $column;
        $this->index = $index;
        $this->table = $table;
    }

    public function getTable(): string
    {
        $result = $this->table;

        return $result;
    }

    public function getColumn(): string
    {
        $result = $this->column;

        return $result;
    }

    public function getIndex(): string
    {
        $result = $this->index;

        return $result;
    }
}
