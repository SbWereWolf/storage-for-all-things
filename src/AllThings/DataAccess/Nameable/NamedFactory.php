<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Nameable;

use JetBrains\PhpStorm\Pure;

class NamedFactory
{
    protected string $code = '';
    protected string $title = '';
    protected string $remark = '';

    #[Pure]
    public function makeNameable(): Nameable
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = (new NamedEntity(
            $this->code,
            $this->title,
            $this->remark,
        ));

        return $result;
    }

    public function setCode(string $value): NamedFactory
    {
        $this->code = $value;

        return $this;
    }

    public function setTitle(string $value): NamedFactory
    {
        $this->title = $value;

        return $this;
    }

    public function setRemark(string $value): NamedFactory
    {
        $this->remark = $value;

        return $this;
    }
}