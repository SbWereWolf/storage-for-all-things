<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\DataAccess\Nameable;

use AllThings\DataAccess\Uniquable\UniquableManager;

interface DataManager extends UniquableManager
{
    public function correct(string $targetIdentity = ''): bool;

    public function browse(): bool;
}
