<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:22
 */

namespace AllThings\SearchEngine;


class DiscreteFilter extends Filter
{
    /**
     * @var array
     */
    private $values;

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
