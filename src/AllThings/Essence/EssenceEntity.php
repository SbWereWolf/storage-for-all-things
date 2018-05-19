<?php
/**
 * Created by PhpStorm.
 * User: СЕРГЕЙ
 * Date: 19.05.2018
 * Time: 13:11
 */

namespace AllThings\Essence;


use AllThings\DataObject\Nameable;
use AllThings\DataObject\NamedEntity;
use AllThings\DataObject\Storable;
use AllThings\DataObject\Storage;

class EssenceEntity implements IEssence
{
    public $nameable = null;
    public $storable = null;

    function __construct(Nameable $nameable, Storable $storable)
    {
        $this->nameable = $nameable;
        $this->storable = $storable;
    }

    static function GetDefaultExemplar(): IEssence
    {

        $nameable = new NamedEntity();
        $storable = new Storage();

        $essence = new EssenceEntity($nameable, $storable);

        return $essence;

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

    function getStoreAt(): \string
    {
        $storage = $this->storable->getStoreAt();

        return $storage;
    }

    function setStoreAt(\string $storeAt): Storable
    {
        $this->storable->setStoreAt($storeAt);

        return $this;
    }

    function GetEssenceCopy(): IEssence
    {
        $nameable = $this->getNameableCopy();
        $storable = $this->getStorableCopy();

        $essence = new EssenceEntity($nameable, $storable);

        return $essence;
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
