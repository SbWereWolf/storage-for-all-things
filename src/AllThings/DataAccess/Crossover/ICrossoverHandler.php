<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */

namespace AllThings\DataAccess\Crossover;

use AllThings\DataAccess\Linkage\ILinkageHandler;

interface ICrossoverHandler extends ILinkageHandler
{
    public function put(ICrossover $crossover): bool;

    public function setSubject(
        ICrossover $crossover
    ): ICrossoverHandler;
}
