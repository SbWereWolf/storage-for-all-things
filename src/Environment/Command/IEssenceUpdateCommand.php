<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace Environment\Command;


use AllThings\Blueprint\Essence\IEssence;

interface IEssenceUpdateCommand
{
    public function getParameter(): string;

    public function getSubject(): IEssence;

}
