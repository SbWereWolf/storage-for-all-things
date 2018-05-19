<?php
/**
 * Created by PhpStorm.
 * User: СЕРГЕЙ
 * Date: 19.05.2018
 * Time: 13:11
 */

namespace AllThings\Essence;


use AllThings\DataObject\Nameable;
use AllThings\DataObject\Storable;

class EssenceEntity implements IEssence
{
    public $nameable = null;
    public $storable = null;

    function __construct(Nameable $nameable, Storable $storable)
    {
        $this->nameable = $nameable;
        $this->storable = $storable;
    }

    function setCode(string $code): Nameable
    {
        $this->nameable->setCode($code);

        return $this;
    }

    function getCode(): string
    {
        $code = $this->nameable->getCode();

        return $code;
    }

    function setTitle(string $title): Nameable
    {
        $this->nameable->setTitle($title);

        return $this;
    }

    function getTitle(): string
    {
        $title = $this->nameable->getTitle();

        return $title;
    }

    function setRemark(string $remark): Nameable
    {
        $this->nameable->setRemark($remark);

        return $this;
    }

    function getRemark(): string
    {
        $remark = $this->nameable->getRemark();

        return $remark;
    }

    function getStorage(): \string
    {
        $storage = $this->storable->getStorage();

        return $storage;
    }

    function setStorage(\string $storage): Storable
    {
        $this->storable->setStorage($storage);

        return $this;
    }


    function getNameableCopy(): Nameable
    {
        $nameableDuplicate = $this->nameable->getNameableCopy();

        return $nameableDuplicate;
    }

    function getStorableCopy(): Storable
    {
        $storableDuplicate = $this->storable->getStorableCopy();

        return $storableDuplicate;
    }
}