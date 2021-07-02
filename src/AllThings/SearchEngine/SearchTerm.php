<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */


namespace AllThings\SearchEngine;


class SearchTerm implements Searchable
{

    private $dataType = '';
    private $rangeType = '';

    public function __construct()
    {
        $this->dataType = self::UNDEFINED;
        $this->rangeType = self::UNDEFINED;
    }

    public function getDataType(): string
    {
        $result = $this->dataType;

        return $result;
    }

    public function setDataType(string $value): Searchable
    {
        $this->dataType = $value;

        return $this;
    }

    public function getRangeType(): string
    {
        $result = $this->rangeType;

        return $result;
    }

    public function setRangeType(string $value): Searchable
    {
        $this->rangeType = $value;

        return $this;
    }

    public function getSearchableCopy(): Searchable
    {
        $copy = (new SearchTerm())->setDataType($this->dataType)->setRangeType($this->rangeType);

        return $copy;
    }
}
