<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:22
 */

namespace Environment\Command;


use AllThings\Essence\IEssence;

interface IEssenceUpdateCommand
{
    public function getParameter(): string;

    public function getSubject(): IEssence;

}
