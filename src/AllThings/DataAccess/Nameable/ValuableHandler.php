<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Nameable;

interface ValuableHandler
{
    public function write(object $named): bool;

    public function read(string $uniqueness): Nameable;
}
