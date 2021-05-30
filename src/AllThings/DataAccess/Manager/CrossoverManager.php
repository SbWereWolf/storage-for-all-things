<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 03.06.18 11:25
 */


namespace AllThings\DataAccess\Manager;


use AllThings\DataObject\ICrossover;

interface CrossoverManager
{
    public function attach(): bool;

    public function store(ICrossover $crossover): bool;

    public function take(ICrossover $crossover): bool;
}
