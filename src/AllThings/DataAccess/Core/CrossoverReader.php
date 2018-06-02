<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 02.06.18 22:14
 */


namespace AllThings\DataAccess\Core;


use AllThings\DataObject\ICrossover;

interface CrossoverReader
{
    function select(ICrossover $entity): bool;
}
