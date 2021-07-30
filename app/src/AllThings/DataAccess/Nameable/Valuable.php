<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:39
 */

namespace AllThings\DataAccess\Nameable;


interface Valuable
{
    public function add(): bool;

    public function write(string $code): bool;

    public function read(): bool;

}
