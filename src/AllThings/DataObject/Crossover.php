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

    public function setRightValue(string $value): ICrossover
    {
        $this->rightKey = $value;

        return $this;
    }

    public function setLeftValue(string $value): ICrossover
    {
        $this->leftKey = $value;

        return $this;
    }

    public function getCrossoverCopy(): ICrossover
    {
        $copy = (new Crossover())
            ->setContent($this->getContent())
            ->setLeftValue($this->getLeftValue())
            ->setRightValue($this->getRightValue());

        return $copy;
    }

    public function setContent(string $value): ICrossover
    {
        $this->value = $value;

        return $this;
    }

    public function getContent(): string
    {
        $result = $this->value;

        return $result;
    }

    public function getLeftValue(): string
    {
        $result = $this->leftKey;

        return $result;
    }

    public function getRightValue(): string
    {
        $result = $this->rightKey;

        return $result;
    }
}
