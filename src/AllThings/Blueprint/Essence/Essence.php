<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\Blueprint\Essence;

use AllThings\DataAccess\Nameable\Nameable;
use AllThings\StorageEngine\Storable;

class Essence implements IEssence
{
    public Nameable $nameable;
    public Storable $storable;

    public function __construct(Nameable $nameable, Storable $storable)
    {
        $this->nameable = $nameable;
        $this->storable = $storable;
    }


    public function getCode(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $code = $this->nameable->getCode();

        return $code;
    }

    public function getTitle(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $title = $this->nameable->getTitle();

        return $title;
    }

    public function getRemark(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $remark = $this->nameable->getRemark();

        return $remark;
    }

    public function getStorageManner(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $storage = $this->storable->getStorageManner();

        return $storage;
    }

    public function GetEssenceCopy(): IEssence
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $nameable = $this->getNameableCopy();
        $storable = $this->getStorableCopy();

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $essence = new Essence($nameable, $storable);

        return $essence;
    }

    public function getNameableCopy(): Nameable
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $nameableDuplicate = $this->nameable->getNameableCopy();

        return $nameableDuplicate;
    }

    public function getStorableCopy(): Storable
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $storableDuplicate = $this->storable->getStorableCopy();

        return $storableDuplicate;
    }
}
