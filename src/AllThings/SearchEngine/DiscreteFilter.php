<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\SearchEngine;

use Exception;

class DiscreteFilter extends Filter
{
    private array $values;

    /**
     * @param string $attribute
     * @param string $dataType
     * @param array  $values
     *
     * @throws Exception
     */
    public function __construct(
        string $attribute,
        string $dataType,
        array $values,
    ) {
        parent::__construct($attribute, $dataType);
        $this->values = $values;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
