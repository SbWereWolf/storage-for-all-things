<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace AllThings\DataAccess\Nameable;

interface ValuableReader
{

    public function select(Nameable $entity): bool;
}
