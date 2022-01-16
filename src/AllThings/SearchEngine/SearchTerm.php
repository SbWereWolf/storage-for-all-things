<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */


namespace AllThings\SearchEngine;


use JetBrains\PhpStorm\Pure;

class SearchTerm implements Searchable
{
    private string $dataType;
    private string $rangeType;

    /**
     * @param string $dataType
     * @param string $rangeType
     */
    public function __construct(
        string $dataType = self::UNDEFINED,
        string $rangeType = self::UNDEFINED
    ) {
        $this->dataType = $dataType;
        $this->rangeType = $rangeType;
    }

    public function getDataType(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $this->dataType;

        return $result;
    }

    public function getRangeType(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $this->rangeType;

        return $result;
    }

    #[Pure]
    public function getSearchableCopy(): Searchable
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $copy = (new SearchTerm(
            $this->getDataType(), $this->getRangeType()
        ));

        return $copy;
    }
}
