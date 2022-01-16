<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Nameable;

use AllThings\DataAccess\Uniquable\UniquableManager;

interface DataManager extends UniquableManager
{
    public function correct(object $named): bool;

    public function browse(string $uniqueness): Nameable;
}
