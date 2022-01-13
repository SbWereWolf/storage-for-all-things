<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 3:02
 */

namespace AllThings\DataAccess\Uniquable;

interface UniquableHandler
{
    public function erase(): bool;

    public function add(): bool;
}
