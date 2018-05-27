<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 27.05.18 14:50
 */

namespace AllThings\DataAccess\Handler;


interface Linkable
{
    function add(array $linkage): bool;

    function remove(array $linkage): bool;

    function getRelated(array $linkage): bool;

}
