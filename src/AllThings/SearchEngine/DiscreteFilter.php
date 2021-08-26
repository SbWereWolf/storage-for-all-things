<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:45
 */

namespace AllThings\SearchEngine;

class DiscreteFilter extends Filter
{
    private array $values;

    public function __construct(string $attribute, array $values)
    {
        parent::__construct($attribute);
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
