<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */


namespace AllThings\DataAccess\Linkage;


class Linkage implements ILinkage
{
    protected string $rightKey = '';
    protected string $leftKey = '';

    public function getLinkageCopy(): ILinkage
    {
        $copy = (new Linkage())
            ->setLeftValue($this->getLeftValue())
            ->setRightValue($this->getRightValue());

        return $copy;
    }

    public function setRightValue(string $value): ILinkage
    {
        $this->rightKey = $value;

        return $this;
    }

    public function setLeftValue(string $value): ILinkage
    {
        $this->leftKey = $value;

        return $this;
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
