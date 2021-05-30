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

    public function getCode(): string
    {
        $code = $this->code;

        return $code;
    }

    public function setCode(string $value): Nameable
    {
        $this->code = $value;

        return $this;
    }

    public function getTitle(): string
    {
        $title = $this->title;

        return $title;
    }

    public function setTitle(string $value): Nameable
    {
        $this->title = $value;

        return $this;
    }

    public function getRemark(): string
    {
        $remark = $this->remark;

        return $remark;
    }

    public function setRemark(string $value): Nameable
    {
        $this->remark = $value;

        return $this;
    }

    public function getNameableCopy(): Nameable
    {
        $copy = new NamedEntity();
        $copy->setCode($this->code)->setTitle($this->title)->setRemark($this->remark);
        return $copy;
    }
}
