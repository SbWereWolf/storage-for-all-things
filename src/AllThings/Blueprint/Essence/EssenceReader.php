<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace AllThings\Blueprint\Essence;


interface EssenceReader
{

    public function select(IEssence $entity): bool;
}
