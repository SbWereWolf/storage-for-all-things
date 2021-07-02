<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:44
 */

namespace AllThings\Attribute;


use AllThings\DataObject\Nameable;
use AllThings\DataObject\Storable;

interface IEssence extends Nameable, Storable
{

    public static function GetDefaultEssence(): IEssence;

    public function GetEssenceCopy(): IEssence;
}
