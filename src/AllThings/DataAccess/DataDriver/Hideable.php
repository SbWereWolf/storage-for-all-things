<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 16:42
 */

namespace AllThings\DataAccess\Implementation;


interface Hideable
{
    function hide (string $code): bool;

}
