<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\Blueprint\Attribute;

use AllThings\DataAccess\Nameable\NamedFactory;
use AllThings\SearchEngine\Searchable;
use AllThings\SearchEngine\SearchTerm;
use Exception;
use JetBrains\PhpStorm\Pure;

class AttributeFactory extends NamedFactory
{
    private string $dataType;
    private string $rangeType;

    #[Pure]
    public function makeAttribute(): IAttribute
    {
        $nameable = $this->makeNameable();
        $searchable = (new SearchTerm(
            $this->dataType,
            $this->rangeType,
        ));

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = new Attribute($nameable, $searchable);

        return $result;
    }

    /**
     * @throws Exception
     */
    public function setDataType(string $value): static
    {
        $isAcceptable = in_array(
            $value,
            Searchable::DATA_TYPES,
            true
        );
        if (!$isAcceptable) {
            throw new Exception(
                'Data type'
                . ' MUST be one of :'
                . ' word | number | time | interval'
                . ", `$value` given"
            );
        }

        $this->dataType = $value;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function setRangeType(string $value): static
    {
        $isAcceptable = in_array(
            $value,
            Searchable::RANGE_TYPES,
            true
        );
        if (!$isAcceptable) {
            throw new Exception(
                'Range type'
                . ' MUST be one of :'
                . ' continuous | discrete'
                . ", `$value` given"
            );
        }

        $this->rangeType = $value;

        return $this;
    }
}