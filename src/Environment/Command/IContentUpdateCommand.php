<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:22
 */

namespace Environment\Command;


use AllThings\DataObject\ICrossover;

interface IContentUpdateCommand
{
    public function getParameter(): ICrossover;

    public function getSubject(): ICrossover;
}
