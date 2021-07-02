<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:44
 */

namespace AllThings\DataAccess\Core;


use AllThings\Attribute\IAttribute;

interface AttributeReader
{

    public function select(IAttribute $entity): bool;
}
