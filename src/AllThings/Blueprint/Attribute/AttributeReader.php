<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace AllThings\Blueprint\Attribute;


interface AttributeReader
{

    public function select(IAttribute $entity): bool;
}
