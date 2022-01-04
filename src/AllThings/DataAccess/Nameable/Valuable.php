<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\DataAccess\Nameable;

interface Valuable
{
    public function write(string $code): bool;

    public function read(): bool;
}
