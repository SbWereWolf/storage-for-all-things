<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:39
 */


namespace AllThings\DataAccess\Crossover;


interface CrossoverManager
{
    public function attach(): bool;

    public function store(ICrossover $crossover): bool;

    public function take(ICrossover $crossover): bool;
}
