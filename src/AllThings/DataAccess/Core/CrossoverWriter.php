<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 23:14
 */

namespace AllThings\DataAccess\Core;


use AllThings\DataObject\ICrossover;

interface CrossoverWriter
{

    function insert(ICrossover $entity): bool;

    function update(ICrossover $targetEntity, ICrossover $suggestionEntity): bool;
}
