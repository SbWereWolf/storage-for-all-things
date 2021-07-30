<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:39
 */

namespace AllThings\DataAccess\Crossover;


interface ICrossoverHandler
{
    public function combine(): bool;

    public function push(ICrossover $crossover): bool;

    public function pull(ICrossover $crossover): bool;
}
