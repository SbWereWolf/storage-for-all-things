<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace Environment\Reception;


use AllThings\DataAccess\Crossover\ICrossover;
use Environment\Command\IContentUpdateCommand;

interface ToCrossover
{
    public function fromPost(): ICrossover;

    public function fromGet(): ICrossover;

    public function fromPut(): IContentUpdateCommand;

}
