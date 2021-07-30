<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:39
 */

namespace AllThings\Blueprint\Attribute;


interface AttributeReader
{

    public function select(IAttribute $entity): bool;
}
