<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\DataAccess\Uniquable;

interface UniquableManager
{
    public function create(): bool;

    public function remove(): bool;
}