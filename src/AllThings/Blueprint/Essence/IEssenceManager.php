<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\Blueprint\Essence;

use AllThings\DataAccess\Nameable\DataManager;

interface IEssenceManager
    extends DataManager
{

    public function correct(object $attribute): bool;

    public function browse(string $uniqueness): IEssence;
}
