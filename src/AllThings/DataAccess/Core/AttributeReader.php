<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 23.05.18 0:15
 */

namespace AllThings\DataAccess\Core;


use AllThings\Essence\IAttribute;

interface AttributeReader
{

    function read(IAttribute $entity): bool;
}
