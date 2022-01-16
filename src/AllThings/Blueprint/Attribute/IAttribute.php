<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\Blueprint\Attribute;


use AllThings\DataAccess\Nameable\Nameable;
use AllThings\SearchEngine\Searchable;

interface IAttribute extends Nameable, Searchable
{
    public function GetAttributeCopy(): IAttribute;
}
