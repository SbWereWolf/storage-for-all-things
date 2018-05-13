<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 12:09
 */

namespace AllThings\DataAccess\Core;


use AllThings\DataObject\Named;

interface ValuableWriter
{

    function addNamed (Named $entity): bool;
    function hideNamed (Named $entity):bool;
    function writeNamed (Named $target_entity, Named $suggestion_entity):bool;
}
