<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 23:00
 */

namespace AllThings\DataAccess\Core;


interface PrimitiveWriter
{

    function addPrimitive (array $linkage): bool;
    function removePrimitive (array $linkage):bool;
}
