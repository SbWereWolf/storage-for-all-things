<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace AllThings\Blueprint\Attribute;


use AllThings\DataAccess\Nameable\Nameable;
use AllThings\SearchEngine\Searchable;

interface IAttribute extends Nameable, Searchable
{

    public static function GetDefaultAttribute(): IAttribute;

    public function GetAttributeCopy(): IAttribute;
}
