<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\SearchEngine;

use Exception;

class ContinuousFilter extends Filter
{
    private string $min;
    private string $max;

    /**
     * @throws Exception
     */
    public function __construct(
        string $attribute,
        string $dataType,
        string $min,
        string $max
    ) {
        parent::__construct($attribute, $dataType);
        $this->min = $min;
        $this->max = $max;
    }

    public function getMin(): string
    {
        return $this->min;
    }

    public function getMax(): string
    {
        return $this->max;
    }
}
