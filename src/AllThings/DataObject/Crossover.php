<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 02.06.18 18:15
 */


namespace AllThings\DataObject;


class Crossover implements ICrossover
{
    private $rightKey = null;
    private $leftKey = null;
    private $value = '';

    function setRightValue(\string $value): ICrossover
    {
        $this->rightKey = $value;

        return $this;
    }

    function setLeftValue(\string $value): ICrossover
    {
        $this->leftKey = $value;

        return $this;
    }

    function getCrossoverCopy(): ICrossover
    {
        $copy = (new Crossover())
            ->setContent($this->getContent())
            ->setLeftValue($this->getLeftValue())
            ->setRightValue($this->getRightValue());

        return $copy;
    }

    function setContent(\string $value): ICrossover
    {
        $this->value = $value;

        return $this;
    }

    function getContent(): \string
    {
        $result = $this->value;

        return $result;
    }

    function getLeftValue(): \string
    {
        $result = $this->leftKey;

        return $result;
    }

    function getRightValue(): \string
    {
        $result = $this->rightKey;

        return $result;
    }
}
