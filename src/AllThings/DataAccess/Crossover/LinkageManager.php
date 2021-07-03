<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 03.07.2021, 17:12
 */

namespace AllThings\DataAccess\Crossover;


interface LinkageManager
{

    public function linkUp(): bool;

    public function breakDown(): bool;

    public function getAssociated(): bool;
}
