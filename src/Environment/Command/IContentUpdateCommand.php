<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace Environment\Command;


use AllThings\DataAccess\Crossover\ICrossover;

interface IContentUpdateCommand
{
    public function getParameter(): ICrossover;

    public function getSubject(): ICrossover;
}
