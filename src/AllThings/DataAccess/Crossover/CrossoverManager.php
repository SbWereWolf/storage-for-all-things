<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */


namespace AllThings\DataAccess\Crossover;


interface CrossoverManager
{
    public function attach(): bool;

    public function store(ICrossover $crossover): bool;

    public function take(ICrossover $crossover): bool;
}
