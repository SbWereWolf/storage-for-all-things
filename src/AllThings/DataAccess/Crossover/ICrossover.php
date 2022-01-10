<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */

namespace AllThings\DataAccess\Crossover;

use AllThings\DataAccess\Linkage\ILinkage;

interface ICrossover extends ILinkage
{
    public function getContent(): string;

    public function setContent(string $value): ICrossover;

    public function getCrossoverCopy(): ICrossover;
}
