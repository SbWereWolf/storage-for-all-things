<?php
/**
 * storage-for-all-things
 * Copyright © 2019 Volkhin Nikolay
 * 01.10.2019, 19:42
 */

namespace AllThings\DataObject;


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
