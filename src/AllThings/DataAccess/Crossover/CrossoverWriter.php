<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace AllThings\DataAccess\Crossover;


interface CrossoverWriter
{

    public function insert(ICrossover $entity): bool;

    public function update(ICrossover $targetEntity, ICrossover $suggestionEntity): bool;
}
