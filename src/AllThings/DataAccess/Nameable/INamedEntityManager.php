<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 17:50
 */

namespace AllThings\DataAccess\Nameable;

use AllThings\DataAccess\Retrievable;

interface INamedEntityManager extends DataManager, Retrievable
{
    public function retrieve(): Nameable;
}
