<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:45
 */

namespace AllThings\DataAccess\Nameable;

interface ValuableReader
{

    public function select(Nameable $entity): bool;
}
