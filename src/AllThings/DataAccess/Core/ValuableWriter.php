<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 12:09
 */

namespace AllThings\DataAccess\Core;


use AllThings\DataObject\Nameable;

interface ValuableWriter
{

    function insert(Nameable $entity): bool;

    function setIsHidden(Nameable $entity): bool;

    function update(Nameable $target_entity, Nameable $suggestion_entity): bool;
}
