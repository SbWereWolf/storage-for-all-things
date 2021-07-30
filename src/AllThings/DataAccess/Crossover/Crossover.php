<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:46
 */


namespace AllThings\DataAccess\Crossover;


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
