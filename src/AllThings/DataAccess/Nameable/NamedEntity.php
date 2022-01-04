<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\DataAccess\Nameable;


class NamedEntity implements Nameable
{

    private string $code = '';
    private string $title = '';
    private string $remark = '';

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
