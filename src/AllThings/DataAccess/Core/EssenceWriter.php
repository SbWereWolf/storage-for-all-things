<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 19.05.18 22:22
 */

namespace AllThings\DataAccess\Core;


use AllThings\Essence\IEssence;

interface EssenceWriter
{

    function add(IEssence $entity): bool;

    function hide(IEssence $entity): bool;

    function write(IEssence $target_entity, IEssence $suggestion_entity): bool;
}
