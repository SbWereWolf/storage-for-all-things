<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:45
 */

namespace AllThings\Blueprint\Essence;


use AllThings\DataAccess\Nameable\Nameable;
use AllThings\DataAccess\Nameable\NamedEntity;
use AllThings\StorageEngine\Storable;
use AllThings\StorageEngine\Storage;
use Exception;

class Essence implements IEssence
{
    public ?Nameable $nameable = null;
    public ?Storable $storable = null;

    public function __construct(Nameable $nameable, Storable $storable)
    {
        $this->nameable = $nameable;
        $this->storable = $storable;
    }

    /**
     * @throws Exception
     */
    public static function GetDefaultEssence(): IEssence
    {
        $nameable = new NamedEntity();
        $storable = new Storage();
        $storable->setStorageKind(static::DIRECT_READING);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $essence = new Essence($nameable, $storable);

        return $essence;
    }

    public function setCode(string $value): Nameable
    {
        $this->nameable->setCode($value);

        return $this;
    }

    /** @noinspection PhpUnnecessaryLocalVariableInspection */
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
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $title = $this->nameable->getTitle();

        return $title;
    }

    public function setRemark(string $value): Nameable
    {
        $this->nameable->setRemark($value);

        return $this;
    }

    /** @noinspection PhpUnnecessaryLocalVariableInspection */
    public function getRemark(): string
    {
        $remark = $this->nameable->getRemark();

        return $remark;
    }

    public function getStorageKind(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $storage = $this->storable->getStorageKind();

        return $storage;
    }

    public function setStorageKind(string $value): Storable
    {
        $this->storable->setStorageKind($value);

        return $this;
    }

    /** @noinspection PhpUnnecessaryLocalVariableInspection */
    public function GetEssenceCopy(): IEssence
    {
        $nameable = $this->getNameableCopy();
        $storable = $this->getStorableCopy();

        $essence = new Essence($nameable, $storable);

        return $essence;
    }

    public function getNameableCopy(): Nameable
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $nameableDuplicate = $this->nameable->getNameableCopy();

        return $nameableDuplicate;
    }

    /** @noinspection PhpUnnecessaryLocalVariableInspection */
    public function getStorableCopy(): Storable
    {
        $storableDuplicate = $this->storable->getStorableCopy();

        return $storableDuplicate;
    }
}
