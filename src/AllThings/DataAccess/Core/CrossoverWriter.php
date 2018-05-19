<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 23:14
 */

namespace AllThings\DataAccess\Core;


use AllThings\DataObject\Crossover;

interface CrossoverWriter
{

    function addCrossover(Crossover $entity): bool;

    function writeCrossover(Crossover $target_entity, Crossover $suggestion_entity): bool;
}
