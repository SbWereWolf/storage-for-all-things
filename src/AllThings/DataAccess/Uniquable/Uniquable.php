<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\DataAccess\Uniquable;

interface Uniquable
{
    public function erase(): bool;

    public function add(): bool;
}
