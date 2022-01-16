<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 11:52
 */

namespace AllThings\DataAccess\Nameable;

interface Nameable
{
    public function getCode(): string;

    public function getTitle(): string;

    public function getRemark(): string;

    public function getNameableCopy(): Nameable;
}
