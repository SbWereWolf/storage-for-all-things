<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\Blueprint\Essence;

use AllThings\DataAccess\Nameable\ValuableHandler;

interface IEssenceHandler extends ValuableHandler
{
    public function read(string $uniqueness): IEssence;

    public function write(object $essence): bool;
}