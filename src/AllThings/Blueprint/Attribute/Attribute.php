<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\Blueprint\Attribute;

use AllThings\DataAccess\Nameable\Nameable;
use AllThings\SearchEngine\Searchable;

class Attribute implements IAttribute
{
    private Nameable $nameable;
    private Searchable $searchable;

    /**
     * @param Nameable   $nameable
     * @param Searchable $searchable
     */
    public function __construct(Nameable $nameable, Searchable $searchable)
    {
        $this->nameable = $nameable;
        $this->searchable = $searchable;
    }

    public function GetAttributeCopy(): IAttribute
    {
        $nameable = $this->nameable->getNameableCopy();
        $searchable = $this->searchable->getSearchableCopy();

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $attribute = new Attribute($nameable, $searchable);

        return $attribute;
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

    public function getNameableCopy(): Nameable
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $nameable = $this->nameable->getNameableCopy();

        return $nameable;
    }

    public function getDataType(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $dataType = $this->searchable->getDataType();

        return $dataType;
    }

    public function getRangeType(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $rangeType = $this->searchable->getRangeType();

        return $rangeType;
    }

    public function getSearchableCopy(): Searchable
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $searchable = $this->searchable->getSearchableCopy();

        return $searchable;
    }
}
