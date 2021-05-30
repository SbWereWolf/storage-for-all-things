<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 22.05.18 22:59
 */

namespace AllThings\Essence;


use AllThings\DataObject\Nameable;
use AllThings\DataObject\Searchable;

interface IAttribute extends Nameable, Searchable
{

    public static function GetDefaultAttribute(): IAttribute;

    public function GetAttributeCopy(): IAttribute;
}
