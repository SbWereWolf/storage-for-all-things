<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:22
 */

namespace AllThings\SearchEngine;


class ContinuousFilter extends Filter
{
    private $min;
    private $max;

    public function __construct(string $attribute, $min, $max)
    {
        parent::__construct($attribute);
        $this->setMin($min)->setMax($max);
    }

    /**
     * @param mixed $max
     *
     * @return ContinuousFilter
     */
    private function setMax($max)
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @param mixed $min
     *
     * @return ContinuousFilter
     */
    private function setMin($min)
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @return mixed
     */
    public function getMax()
    {
        return $this->max;
    }
}
