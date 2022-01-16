<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\Blueprint\Attribute;

use AllThings\DataAccess\Nameable\ValuableHandler;

interface IAttributeHandler extends ValuableHandler
{
    public function write(object $attribute): bool;

    public function read(string $uniqueness): IAttribute;
}
