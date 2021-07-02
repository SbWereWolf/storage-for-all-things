<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 14.05.2018 Time: 0:07
 */

namespace AllThings\DataAccess\Crossover;


interface ColumnReader
{

    public function select(array $linkage): bool;
}
