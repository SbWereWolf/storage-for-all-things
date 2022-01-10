<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */

namespace AllThings\DataAccess\Linkage;

use AllThings\DataAccess\Crossover\ICrossover;

interface PrimitiveWriter
{

    public function insert(ICrossover $linkage): bool;

    public function delete(ICrossover $linkage): bool;
}
