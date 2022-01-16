<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\Blueprint\Essence;


use AllThings\DataAccess\Nameable\Nameable;
use AllThings\StorageEngine\Storable;

interface IEssence extends Nameable, Storable
{
    public function GetEssenceCopy(): IEssence;
}
