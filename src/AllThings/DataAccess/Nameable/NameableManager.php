<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 9:02
 */

namespace AllThings\DataAccess\Nameable;

use AllThings\DataAccess\DataTransfer\Retrievable;

interface NameableManager extends DataManager, Retrievable
{
    public function retrieve(): Nameable;
}
