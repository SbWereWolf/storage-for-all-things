<?php
/**
 * storage-for-all-things
 * Copyright © 2019 Volkhin Nikolay
 * 01.10.2019, 19:37
 */

namespace AllThings\DataObject;


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
