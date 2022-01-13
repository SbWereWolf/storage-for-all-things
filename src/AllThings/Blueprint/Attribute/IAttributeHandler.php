<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 3:02
 */

namespace AllThings\Blueprint\Attribute;

use AllThings\DataAccess\DataTransfer\Haves;
use AllThings\DataAccess\DataTransfer\Retrievable;
use AllThings\DataAccess\Nameable\ValuableHandler;

interface IAttributeHandler
    extends ValuableHandler,
            Retrievable,
            Haves
{
    public function retrieve(): IAttribute;

    public function setAttribute(
        IAttribute $stuff
    ): IAttributeHandler;
}
