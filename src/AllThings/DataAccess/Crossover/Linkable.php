<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:46
 */

namespace AllThings\DataAccess\Crossover;


interface Linkable
{
    public function add(ICrossover $linkage): bool;

    public function remove(ICrossover $linkage): bool;

    public function getRelated(ICrossover $linkage): bool;

}
