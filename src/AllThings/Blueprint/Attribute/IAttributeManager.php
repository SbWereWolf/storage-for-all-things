<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 17:50
 */

namespace AllThings\Blueprint\Attribute;


use AllThings\DataAccess\Haves;
use AllThings\DataAccess\Nameable\DataManager;
use AllThings\DataAccess\Retrievable;

interface IAttributeManager
    extends DataManager,
            Retrievable,
            Haves
{

    public function retrieve(): IAttribute;

    public function getLocation(): string;

    public function getFormat(): string;

}
