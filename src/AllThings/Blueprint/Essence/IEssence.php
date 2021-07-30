<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:45
 */

namespace AllThings\Blueprint\Essence;


use AllThings\DataAccess\Nameable\Nameable;
use AllThings\StorageEngine\Storable;

interface IEssence extends Nameable, Storable
{

    public static function GetDefaultEssence(): IEssence;

    public function GetEssenceCopy(): IEssence;
}
