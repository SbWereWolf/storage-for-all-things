<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace AllThings\DataAccess\Crossover;


interface ICrossoverTable
{
    public function getTableName(): string;

    public function getLeftColumn(): string;

    public function getRightColumn(): string;

}
