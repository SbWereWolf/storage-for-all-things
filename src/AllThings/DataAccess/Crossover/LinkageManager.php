<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace AllThings\DataAccess\Crossover;


interface LinkageManager
{

    public function setUp(): bool;

    public function breakDown(): bool;

    public function getAssociated(): bool;
}
