<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 11:52
 */

namespace AllThings\DataAccess\Nameable;


interface Nameable
{
    public function setCode(string $value): Nameable;

    public function getCode(): string;

    public function setTitle(string $value): Nameable;

    public function getTitle(): string;

    public function setRemark(string $value): Nameable;

    public function getRemark(): string;

    public function getNameableCopy(): Nameable;
}
