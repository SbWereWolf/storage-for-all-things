<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:46
 */

namespace AllThings\DataAccess\Crossover;


interface LinkageManager
{

    public function linkUp(ICrossover $linkage): bool;

    public function breakDown(ICrossover $linkage): bool;

    public function getAssociated(ICrossover $linkage): bool;
}