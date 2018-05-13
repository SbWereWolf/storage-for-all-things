<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 16:56
 */

namespace AllThings\DataObject;


class NamedEntity implements Named
{

    private $code = '';
    private $title = '';
    private $remark = '';

    function setCode(string $code): Named
    {
        $this->code = $code;

        return $this;
    }

    function getCode(): string
    {
        $code = $this->code;

        return $code;
    }

    function setTitle(string $title): Named
    {
        $this->title = $title;

        return $this;
    }

    function getTitle(): string
    {
        $title = $this->title;

        return $title;
    }

    function setRemark(string $remark): Named
    {
        $this->remark = $remark;

        return $this;
    }

    function getRemark(): string
    {
        $remark = $this->remark;

        return $remark;
    }

    function getDuplicate(): Named
    {
        $copy = new NamedEntity();
        $copy->setCode($this->code)->setTitle($this->title)->setRemark($this->remark);
        return $copy;
    }
}
