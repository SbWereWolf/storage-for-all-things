<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:45
 */

namespace AllThings\SearchEngine;

class ContinuousFilter extends Filter
{
    private string $min;
    private string $max;

    public function __construct(
        string $attribute,
        string $min,
        string $max
    )
    {
        parent::__construct($attribute);
        $this->setMin($min)->setMax($max);
    }

    /**
     * @param mixed $max
     *
     * @return ContinuousFilter
     */
    private function setMax(string $max): self
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @param mixed $min
     *
     * @return ContinuousFilter
     */
    private function setMin(string $min): self
    {
        $this->min = $min;
        return $this;
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
