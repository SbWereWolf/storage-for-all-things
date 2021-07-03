<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 03.07.2021, 10:08
 */

namespace AllThings\Blueprint\Essence;


use AllThings\DataAccess\Nameable\Nameable;
use AllThings\DataAccess\Nameable\NamedEntity;
use AllThings\StorageEngine\Storable;
use AllThings\StorageEngine\Storage;

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
        $storable->setStorage(static::DIRECT_READING);

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

    public function getStorage(): string
    {
        $storage = $this->storable->getStorage();

        return $storage;
    }

    public function setStorage(string $value): Storable
    {
        $this->storable->setStorage($value);

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
