<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 03.07.2021, 17:12
 */


namespace AllThings\SearchEngine;


use Exception;

class SearchTerm implements Searchable
{

    private $dataType = '';
    private $rangeType = '';

    public function __construct()
    {
        $this->dataType = static::UNDEFINED;
        $this->rangeType = static::UNDEFINED;
    }

    public function getDataType(): string
    {
        $result = $this->dataType;

        return $result;
    }

    public function setDataType(string $value): Searchable
    {
        $isAcceptable = in_array(
            $value,
            static::DATA_TYPE,
            true
        );
        if (!$isAcceptable) {
            throw new Exception('Data type'
                . ' MUST be one of :'
                . ' symbols | decimal | timestamp'
                . ", `$value` given");
        }

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
        $isAcceptable = in_array(
            $value,
            static::RANGE_TYPE,
            true
        );
        if (!$isAcceptable) {
            throw new Exception('Range type'
                . ' MUST be one of :'
                . ' continuous | discrete'
                . ", `$value` given");
        }

        $this->rangeType = $value;

        return $this;
    }

    public function getSearchableCopy(): Searchable
    {
        $copy = (new SearchTerm())
            ->setDataType($this->dataType)
            ->setRangeType($this->rangeType);

        return $copy;
    }
}
