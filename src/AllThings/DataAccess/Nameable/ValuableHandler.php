<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 3:02
 */

namespace AllThings\DataAccess\Nameable;

interface ValuableHandler
{
    public function write(string $code): bool;

    public function read(): bool;
}
