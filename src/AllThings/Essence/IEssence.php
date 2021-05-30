<?php

namespace AllThings\Essence;


use AllThings\DataObject\Nameable;
use AllThings\DataObject\Storable;

interface IEssence extends Nameable, Storable
{

    public static function GetDefaultEssence(): IEssence;

    public function GetEssenceCopy(): IEssence;
}
