<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 16:56
 */

namespace AllThings\DataObject;


class NamedEntity implements Nameable
{

    private $code = '';
    private $title = '';
    private $remark = '';

    function getCode(): string
    {
        $code = $this->code;

        return $code;
    }

    function setCode(string $value): Nameable
    {
        $this->code = $value;

        return $this;
    }

    function getTitle(): string
    {
        $title = $this->title;

        return $title;
    }

    function setTitle(string $value): Nameable
    {
        $this->title = $value;

        return $this;
    }

    function getRemark(): string
    {
        $remark = $this->remark;

        return $remark;
    }

    function setRemark(string $value): Nameable
    {
        $this->remark = $value;

        return $this;
    }

    function getNameableCopy(): Nameable
    {
        $copy = new NamedEntity();
        $copy->setCode($this->code)->setTitle($this->title)->setRemark($this->remark);
        return $copy;
    }
}
