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

    function getTable(): \string
    {
        $result = $this->table;

        return $result;
    }

    function setTable(\string $value): IForeignKey
    {
        $this->table = $value;

        return $this;
    }

    function getColumn(): \string
    {
        $result = $this->column;

        return $result;
    }

    function setColumn(\string $value): IForeignKey
    {
        $this->column = $value;

        return $this;
    }

    function getIndex(): \string
    {
        $result = $this->index;

        return $result;
    }

    function setIndex(\string $value): IForeignKey
    {
        $this->index = $value;

        return $this;
    }
}
