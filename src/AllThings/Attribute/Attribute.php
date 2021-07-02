<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:44
 */

namespace AllThings\Attribute;


use AllThings\DataObject\Nameable;
use AllThings\DataObject\NamedEntity;
use AllThings\DataObject\Searchable;
use AllThings\DataObject\SearchTerm;

class Attribute implements IAttribute
{
    private $nameable = null;
    private $searchable = null;

    public function __construct(Nameable $nameable, Searchable $searchable)
    {
        $this->nameable = $nameable;
        $this->searchable = $searchable;
    }

    public static function GetDefaultAttribute(): IAttribute
    {
        $nameable = new NamedEntity();
        $searchable = new SearchTerm();

        $attribute = new Attribute($nameable, $searchable);

        return $attribute;
    }

    public function GetAttributeCopy(): IAttribute
    {
        $nameable = $this->nameable->getNameableCopy();
        $searchable = $this->searchable->getSearchableCopy();

        $attribute = new Attribute($nameable, $searchable);

        return $attribute;
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

    public function getNameableCopy(): Nameable
    {
        $nameable = $this->nameable->getNameableCopy();

        return $nameable;
    }

    public function getDataType(): string
    {
        $dataType = $this->searchable->getDataType();

        return $dataType;
    }

    public function setDataType(string $value): Searchable
    {
        $this->searchable->setDataType($value);

        return $this;
    }

    public function getRangeType(): string
    {
        $rangeType = $this->searchable->getRangeType();

        return $rangeType;
    }

    public function setRangeType(string $value): Searchable
    {
        $this->searchable->setRangeType($value);

        return $this;
    }

    public function getSearchableCopy(): Searchable
    {
        $searchable = $this->searchable->getSearchableCopy();

        return $searchable;
    }
}
