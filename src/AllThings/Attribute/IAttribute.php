<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:44
 */

namespace AllThings\Attribute;


use AllThings\DataObject\Nameable;
use AllThings\DataObject\Searchable;

interface IAttribute extends Nameable, Searchable
{

    public static function GetDefaultAttribute(): IAttribute;

    public function GetAttributeCopy(): IAttribute;
}
