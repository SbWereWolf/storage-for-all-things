<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */

namespace AllThings\DataAccess\Crossover;

use AllThings\DataAccess\Linkage\LinkageWriter;

interface CrossoverWriter extends LinkageWriter
{
    public function update(
        ICrossover $targetEntity,
        ICrossover $suggestionEntity
    ): bool;
}
