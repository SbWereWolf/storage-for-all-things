<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 20:27
 */

namespace AllThings\Essence;


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

    static function GetDefaultAttribute(): IAttribute
    {
        $nameable = new NamedEntity();
        $searchable = new SearchTerm();

        $attribute = new Attribute($nameable, $searchable);

        return $attribute;
    }

    function GetAttributeCopy(): IAttribute
    {
        $nameable = $this->nameable->getNameableCopy();
        $searchable = $this->searchable->getSearchableCopy();

        $attribute = new Attribute($nameable, $searchable);

        return $attribute;
    }

    function setCode(string $value): Nameable
    {
        $this->nameable->setCode($value);

        return $this;
    }

    function getCode(): string
    {
        $code = $this->nameable->getCode();

        return $code;
    }

    function setTitle(string $value): Nameable
    {
        $this->nameable->setTitle($value);

        return $this;
    }

    function getTitle(): string
    {
        $title = $this->nameable->getTitle();

        return $title;
    }

    function setRemark(string $value): Nameable
    {
        $this->nameable->setRemark($value);

        return $this;
    }

    function getRemark(): string
    {
        $remark = $this->nameable->getRemark();

        return $remark;
    }

    function getNameableCopy(): Nameable
    {
        $nameable = $this->nameable->getNameableCopy();

        return $nameable;
    }

    function getDataType(): string
    {
        $dataType = $this->searchable->getDataType();

        return $dataType;
    }

    function setDataType(string $value): Searchable
    {
        $this->searchable->setDataType($value);

        return $this;
    }

    function getRangeType(): string
    {
        $rangeType = $this->searchable->getRangeType();

        return $rangeType;
    }

    function setRangeType(string $value): Searchable
    {
        $this->searchable->setRangeType($value);

        return $this;
    }

    function getSearchableCopy(): Searchable
    {
        $searchable = $this->searchable->getSearchableCopy();

        return $searchable;
    }
}
