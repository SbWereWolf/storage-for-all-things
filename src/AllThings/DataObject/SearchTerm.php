<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 22.05.18 23:39
 */


namespace AllThings\DataObject;


class SearchTerm implements Searchable
{

    private $dataType = '';
    private $rangeType = '';

    public function __construct()
    {
        $this->dataType = self::UNDEFINED;
        $this->rangeType = self::UNDEFINED;
    }

    function getDataType(): \string
    {
        $result = $this->dataType;

        return $result;
    }

    function setDataType(\string $value): Searchable
    {
        $this->dataType = $value;

        return $this;
    }

    function getRangeType(): \string
    {
        $result = $this->rangeType;

        return $result;
    }

    function setRangeType(\string $value): Searchable
    {
        $this->rangeType = $value;

        return $this;
    }

    function getSearchableCopy(): Searchable
    {
        $copy = (new SearchTerm())->$this->setDataType($this->dataType)->setRangeType($this->rangeType);

        return $copy;
    }
}
