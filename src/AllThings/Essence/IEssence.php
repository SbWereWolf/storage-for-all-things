<?php

namespace AllThings\Essence;


use AllThings\DataObject\Nameable;
use AllThings\DataObject\Storable;

interface IEssence extends Nameable, Storable
{

    static function GetDefaultEssence(): IEssence;

    function GetEssenceCopy(): IEssence;
}
