<?php

namespace AllThings\Essence;


use AllThings\DataObject\Nameable;
use AllThings\DataObject\NamedEntity;
use AllThings\DataObject\Storable;
use AllThings\DataObject\Storage;

class Essence implements IEssence
{
    public $nameable = null;
    public $storable = null;

    public function __construct(Nameable $nameable, Storable $storable)
    {
        $this->nameable = $nameable;
        $this->storable = $storable;
    }

    public static function GetDefaultEssence(): IEssence
    {
        $nameable = new NamedEntity();
        $storable = new Storage();

        $essence = new Essence($nameable, $storable);

        return $essence;
    }

    public function setCode(string $value): Nameable
    {
        $this->nameable->setCode($value);

        return $this;
    }

    public function getCode(): string
    {
        $code = $this->nameable->getCode();

        return $code;
    }

    public function setTitle(string $value): Nameable
    {
        $this->nameable->setTitle($value);

        return $this;
    }

    public function getTitle(): string
    {
        $title = $this->nameable->getTitle();

        return $title;
    }

    public function setRemark(string $value): Nameable
    {
        $this->nameable->setRemark($value);

        return $this;
    }

    public function getRemark(): string
    {
        $remark = $this->nameable->getRemark();

        return $remark;
    }

    public function getStoreAt(): string
    {
        $storage = $this->storable->getStoreAt();

        return $storage;
    }

    public function setStoreAt(string $value): Storable
    {
        $this->storable->setStoreAt($value);

        return $this;
    }

    public function GetEssenceCopy(): IEssence
    {
        $nameable = $this->getNameableCopy();
        $storable = $this->getStorableCopy();

        $essence = new Essence($nameable, $storable);

        return $essence;
    }

    public function getNameableCopy(): Nameable
    {
        $nameableDuplicate = $this->nameable->getNameableCopy();

        return $nameableDuplicate;
    }

    public function getStorableCopy(): Storable
    {
        $storableDuplicate = $this->storable->getStorableCopy();

        return $storableDuplicate;
    }
}
