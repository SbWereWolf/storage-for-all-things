<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */


namespace AllThings\DataAccess\Crossover;


interface IForeignKey
{
    public function getTable(): string;

    public function getColumn(): string;

    public function getIndex(): string;

}
