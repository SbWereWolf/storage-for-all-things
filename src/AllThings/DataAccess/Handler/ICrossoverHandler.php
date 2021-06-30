<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 01.07.2021, 1:42
 */

namespace AllThings\DataAccess\Handler;


use AllThings\DataObject\ICrossover;

interface ICrossoverHandler
{
    public function combine(): bool;

    public function push(ICrossover $crossover): bool;

    public function pull(ICrossover $crossover): bool;
}
