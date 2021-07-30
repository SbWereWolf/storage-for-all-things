<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:40
 */


namespace AllThings\DataAccess\Crossover;


interface IForeignKey
{
    public function getTable(): string;

    public function getColumn(): string;

    public function getIndex(): string;

}
