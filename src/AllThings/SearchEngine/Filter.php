<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\SearchEngine;


use Exception;

class Filter implements Filtering
{
    /**
     * @var string
     */
    private string $attribute;
    private string $dataType;

    /**
     * @param string $attribute
     * @param string $dataType
     *
     * @throws Exception
     */
    public function __construct(string $attribute, string $dataType)
    {
        if (!in_array($dataType, Searchable::DATA_TYPES)) {
            throw new Exception(
                'Data type MUST be one of :'
                . ' word | number | time | interval'
                . ", `$dataType` given"
            );
        }
        $this->attribute = $attribute;
        $this->dataType = $dataType;
    }

    /**
     * @return string
     */
    public function getAttribute(): string
    {
        return $this->attribute;
    }

    /**
     * @return string
     */
    public function getDataType(): string
    {
        return $this->dataType;
    }

}
