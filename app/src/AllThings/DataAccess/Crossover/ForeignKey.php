<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:39
 */


namespace AllThings\DataAccess\Crossover;


class ForeignKey implements IForeignKey
{

    private $table = '';
    private $column = '';
    private $index = '';

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
