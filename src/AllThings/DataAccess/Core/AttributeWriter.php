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

    function add(IAttribute $entity): bool;

    function hide(IAttribute $entity): bool;

    function write(IAttribute $target_entity, IAttribute $suggestion_entity): bool;
}
