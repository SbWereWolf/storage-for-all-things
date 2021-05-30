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

    public function insert(ICrossover $entity): bool;

    public function update(ICrossover $targetEntity, ICrossover $suggestionEntity): bool;
}
