<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 15:49
 */

namespace AllThings\DataAccess\Core;

use AllThings\DataObject\Nameable;

interface ValuableReader
{

    public function select(Nameable $entity): bool;
}
