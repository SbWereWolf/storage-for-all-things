<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 02.06.18 21:47
 */

namespace AllThings\DataAccess\Handler;


use AllThings\DataObject\ICrossover;

interface ICrossoverHandler
{
    public function crossing(): bool;

    public function setCrossover(ICrossover $crossover): bool;

    public function getCrossover(ICrossover $crossover): bool;
}
