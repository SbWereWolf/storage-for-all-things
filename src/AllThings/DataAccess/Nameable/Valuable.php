<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace AllThings\DataAccess\Nameable;


interface Valuable
{
    public function add(): bool;

    public function write(string $code): bool;

    public function read(): bool;

}
