<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 16:37
 */

namespace AllThings\DataAccess\Implementation;


interface Valuable
{
    function insert(string $code): bool;

    function write(string $code): bool;

    function read(string $code): bool;

}
