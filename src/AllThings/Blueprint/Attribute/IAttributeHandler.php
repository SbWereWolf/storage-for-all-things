<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 9:02
 */

namespace AllThings\Blueprint\Attribute;

use AllThings\DataAccess\DataTransfer\Haves;
use AllThings\DataAccess\DataTransfer\Retrievable;
use AllThings\DataAccess\Nameable\Valuable;

interface IAttributeHandler
    extends Valuable,
            Retrievable,
            Haves
{
    public function retrieve(): IAttribute;

    public function setAttribute(
        IAttribute $stuff
    ): IAttributeHandler;
}
