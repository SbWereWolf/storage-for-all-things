<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:46
 */

namespace AllThings\DataAccess\Crossover;


interface PrimitiveWriter
{

    public function insert(ICrossover $linkage): bool;

    public function delete(ICrossover $linkage): bool;
}
