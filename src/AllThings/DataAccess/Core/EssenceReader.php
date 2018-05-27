<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 19.05.18 22:33
 */

namespace AllThings\DataAccess\Core;


use AllThings\Essence\IEssence;

interface EssenceReader
{

    function select(IEssence $entity): bool;
}
