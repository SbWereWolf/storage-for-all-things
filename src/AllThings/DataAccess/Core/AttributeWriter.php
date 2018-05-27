<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 23.05.18 0:29
 */

namespace AllThings\DataAccess\Core;


use AllThings\Essence\IAttribute;

interface AttributeWriter
{

    function insert(IAttribute $entity): bool;

    function setIsHidden(IAttribute $entity): bool;

    function update(IAttribute $target_entity, IAttribute $suggestion_entity): bool;
}
