<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 27.05.18 10:43
 */


namespace AllThings\DataObject;


class ForeignKey implements IForeignKey
{

    private $table = '';
    private $column = '';
    private $index = '';

    public function __construct(\string $table, \string $column, \string $index)
    {
        $this->column = $column;
        $this->index = $index;
        $this->table = $table;
    }

    function getTable(): \string
    {
        $result = $this->table;

        return $result;
    }

    function getColumn(): \string
    {
        $result = $this->column;

        return $result;
    }

    function getIndex(): \string
    {
        $result = $this->index;

        return $result;
    }
}