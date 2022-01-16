<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Nameable;


class NamedEntity implements Nameable
{
    private string $code;
    private string $title;
    private string $remark;

    public function __construct(
        string $code,
        string $title = '',
        string $remark = '',
    ) {
        $this->code = $code;
        $this->title = $title;
        $this->remark = $remark;
    }

    public function getCode(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $code = $this->code;

        return $code;
    }

    public function getTitle(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $title = $this->title;

        return $title;
    }

    public function getRemark(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $remark = $this->remark;

        return $remark;
    }

    public function getNameableCopy(): Nameable
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $copy = (new NamedFactory())
            ->setCode($this->code)
            ->setTitle($this->title)
            ->setRemark($this->remark)
            ->makeNameable();

        return $copy;
    }
}
