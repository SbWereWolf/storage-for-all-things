<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:45
 */

namespace AllThings\DataAccess\Crossover;


interface CrossoverWriter
{

    public function insert(ICrossover $entity): bool;

    public function update(ICrossover $targetEntity, ICrossover $suggestionEntity): bool;
}
