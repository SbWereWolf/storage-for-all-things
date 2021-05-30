<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 16:37
 */

namespace AllThings\DataAccess\Handler;


interface Valuable
{
    public function add(): bool;

    public function write(string $code): bool;

    public function read(): bool;

}
