<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */

namespace AllThings\DataAccess\Crossover;

use AllThings\DataAccess\Linkage\Linkage;

class Crossover extends Linkage implements ICrossover
{
    private string $value = '';

    public function getCrossoverCopy(): ICrossover
    {
        $copy = (new Crossover())
            ->setContent($this->getContent());

        $copy->setLeftValue($this->getLeftValue())
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
}
