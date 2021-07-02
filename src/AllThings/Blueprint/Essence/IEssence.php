<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace AllThings\Blueprint\Essence;


use AllThings\DataAccess\Nameable\Nameable;
use AllThings\StorageEngine\Storable;

interface IEssence extends Nameable, Storable
{

    public static function GetDefaultEssence(): IEssence;

    public function GetEssenceCopy(): IEssence;
}
