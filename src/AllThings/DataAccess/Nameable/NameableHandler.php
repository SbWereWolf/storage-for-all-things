<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 3:02
 */

namespace AllThings\DataAccess\Nameable;

use AllThings\DataAccess\DataTransfer\Haves;
use AllThings\DataAccess\DataTransfer\Retrievable;

interface NameableHandler extends ValuableHandler,
                                  Retrievable,
                                  Haves
{
    public function setNamed(Nameable $named): NameableHandler;

    public function retrieve(): Nameable;
}