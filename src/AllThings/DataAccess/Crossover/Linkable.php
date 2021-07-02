<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace AllThings\DataAccess\Crossover;


interface Linkable
{
    public function add(array $linkage): bool;

    public function remove(array $linkage): bool;

    public function getRelated(array $linkage): bool;

}
